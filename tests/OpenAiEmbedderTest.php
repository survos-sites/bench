<?php

declare(strict_types=1);

namespace App\Tests;

use App\Search\OpenAiEmbedder;
use PHPUnit\Framework\TestCase;
use Symfony\AI\Platform\Bridge\OpenAi\Embeddings\ResultConverter;
use Symfony\AI\Platform\PlatformInterface;
use Symfony\AI\Platform\Result\DeferredResult;
use Symfony\AI\Platform\Result\RawHttpResult;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class OpenAiEmbedderTest extends TestCase
{
    public function testIndividualTextCacheSurvivesBatchChangesAndSeparatesDimensions(): void
    {
        $directory = sys_get_temp_dir().'/bench-openai-'.bin2hex(random_bytes(8));
        $platform = $this->createMock(PlatformInterface::class);
        $platform->expects(self::exactly(3))->method('invoke')->willReturnCallback(static function ($model, $input, $options): DeferredResult {
            self::assertSame('text-embedding-3-large', $model);
            $vectors = [];
            foreach ($input as $i => $text) {
                self::assertStringNotContainsString('search_document:', $text);
                $vectors[] = ['index' => $i, 'embedding' => array_fill(0, $options['dimensions'], strlen($text) / 10)];
            }
            $client = new MockHttpClient(new MockResponse(json_encode(['data' => $vectors])));

            return new DeferredResult(new ResultConverter(), new RawHttpResult($client->request('GET', 'https://test.invalid')));
        });
        try {
            $embedder = new OpenAiEmbedder($platform, $directory.'/cache.db', 'text-embedding-3-large', 3);
            $first = $embedder->embed(['a', 'bb'], $embedder->digest());
            self::assertSame([$first[1], $first[0]], $embedder->embed(['bb', 'a'], $embedder->digest(), query: true));
            self::assertCount(2, $embedder->embed(['bb', 'ccc'], $embedder->digest()));
            $other = new OpenAiEmbedder($platform, $directory.'/cache.db', 'text-embedding-3-large', 2);
            self::assertCount(2, $other->embed(['a'], $other->digest())[0]);
            $this->expectExceptionMessage('does not match');
            $other->embed(['a'], $embedder->digest());
        } finally {
            (new Filesystem())->remove($directory);
        }
    }
}
