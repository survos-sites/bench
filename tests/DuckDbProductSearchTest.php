<?php

declare(strict_types=1);

namespace App\Tests;

use App\Search\DuckDbProductSearch;
use App\Search\NomicEmbedder;
use PHPUnit\Framework\TestCase;
use Survos\FetchBundle\Contract\DTO\CachedFetchResult;
use Survos\FetchBundle\Contract\PersistentFetcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class DuckDbProductSearchTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir().'/bench-duckdb-'.bin2hex(random_bytes(8));
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
    }

    public function testSnapshotRoundTripFilteringAndEmbeddingCache(): void
    {
        $embeddingCalls = 0;
        $http = new MockHttpClient(static function (string $method, string $url, array $options) use (&$embeddingCalls): MockResponse {
            if (str_ends_with($url, '/api/tags')) {
                return new MockResponse(json_encode(['models' => [['name' => 'nomic-embed-text:latest', 'digest' => 'test-digest']]], JSON_THROW_ON_ERROR));
            }
            ++$embeddingCalls;
            $inputs = json_decode($options['body'], true, flags: JSON_THROW_ON_ERROR)['input'];
            $vectors = [];
            foreach ($inputs as $i => $input) {
                self::assertMatchesRegularExpression('/^search_(document|query): /', $input);
                $vector = array_fill(0, 768, 0.0);
                $vector[$i] = 1.0;
                $vectors[] = $vector;
            }
            return new MockResponse(json_encode(['embeddings' => $vectors], JSON_THROW_ON_ERROR));
        });
        $embedder = new NomicEmbedder($http, $this->directory.'/embeddings.db', 'http://ollama.test');
        $fetcher = $this->createStub(PersistentFetcherInterface::class);
        $products = [
            ['sku' => 'laptop', 'title' => 'Laptop', 'description' => 'A portable computer.', 'category' => 'laptops', 'price' => 999],
            ['sku' => 'perfume', 'title' => 'Perfume', 'description' => 'A floral scent.', 'category' => 'fragrances', 'price' => 25],
        ];
        $fetcher->method('fetch')->willReturn($this->response(['products' => $products, 'total' => 2]));
        $search = new DuckDbProductSearch($fetcher, $embedder, $this->directory);
        self::assertNull($search->snapshot());
        self::assertSame(2, $search->index()['count']);
        $snapshot = $search->snapshot();
        $path = $this->directory.'/'.$snapshot['file'];
        $hash = hash_file('sha256', $path);
        $result = $search->search('gifts for a techie');
        self::assertSame('laptop', $result['results'][0]['product']['sku']);
        self::assertEqualsWithDelta(0.0, $result['results'][0]['distance'], 1e-9);
        $filtered = $search->search('gifts for a techie', 'fragrances');
        self::assertCount(1, $filtered['results']);
        self::assertSame('perfume', $filtered['results'][0]['product']['sku']);
        self::assertSame(2, $embeddingCalls);
        self::assertSame($hash, hash_file('sha256', $path));
        $search->index();
        self::assertNotSame($snapshot['file'], $search->snapshot()['file']);
        self::assertFileExists($path);
        self::assertSame(2, $embeddingCalls);
    }

    public function testIncompleteDownloadCannotReplaceCurrentSnapshot(): void
    {
        mkdir($this->directory, 0775, true);
        $current = '{"file":"existing.duckdb"}';
        file_put_contents($this->directory.'/current.json', $current);
        $http = new MockHttpClient(new MockResponse('{"models":[{"name":"nomic-embed-text","digest":"test"}]}'));
        $fetcher = $this->createStub(PersistentFetcherInterface::class);
        $fetcher->method('fetch')->willReturn($this->response(['products' => [], 'total' => 2]));
        $search = new DuckDbProductSearch($fetcher, new NomicEmbedder($http, $this->directory.'/cache.db', 'http://ollama.test'), $this->directory);
        try {
            $search->index();
            self::fail('Incomplete download should fail.');
        } catch (\RuntimeException $exception) {
            self::assertStringContainsString('incomplete', $exception->getMessage());
            self::assertSame($current, file_get_contents($this->directory.'/current.json'));
        }
    }

    /** @param array<string, mixed> $payload */
    private function response(array $payload): CachedFetchResult
    {
        return new CachedFetchResult(
            url: DuckDbProductSearch::SOURCE,
            finalUrl: DuckDbProductSearch::SOURCE,
            method: 'GET',
            statusCode: 200,
            contentType: 'application/json',
            contentLength: null,
            contents: json_encode($payload, JSON_THROW_ON_ERROR),
            errorMessage: null,
            fetchedAt: time(),
        );
    }
}
