<?php

declare(strict_types=1);

namespace App\Tests;

use App\Search\DuckDbSearch;
use App\Search\EmbeddingProfiles;
use App\Search\NomicEmbedder;
use App\Search\OpenAiEmbedder;
use App\Search\SearchDatasets;
use App\Search\SnapshotRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Survos\FetchBundle\Contract\DTO\CachedFetchResult;
use Survos\FetchBundle\Contract\PersistentFetcherInterface;
use Symfony\AI\Platform\PlatformInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class DuckDbProductSearchTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir().'/bench-duckdb-'.bin2hex(random_bytes(8));
        mkdir($this->directory);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
    }

    public function testSnapshotRoundTripFilteringAndEmbeddingCache(): void
    {
        $calls = 0;
        $http = new MockHttpClient(static function (string $method, string $url, array $options) use (&$calls): MockResponse {
            if (str_ends_with($url, '/api/tags')) {
                return new MockResponse('{"models":[{"name":"nomic-embed-text:latest","digest":"test-digest"}]}');
            }
            ++$calls;
            $inputs = json_decode($options['body'], true)['input'];
            $vectors = [];
            foreach ($inputs as $i => $input) {
                self::assertMatchesRegularExpression('/^search_(document|query): /', $input);
                $vector = array_fill(0, 768, 0.0);
                $vector[$i] = 1.0;
                $vectors[] = $vector;
            }

            return new MockResponse(json_encode(['embeddings' => $vectors]));
        });
        $fetcher = $this->createStub(PersistentFetcherInterface::class);
        $products = [
            ['sku' => 'laptop', 'title' => 'Laptop', 'description' => 'A portable computer.', 'category' => 'laptops', 'price' => 999],
            ['sku' => 'perfume', 'title' => 'Perfume', 'description' => 'A floral scent.', 'category' => 'fragrances', 'price' => 25],
        ];
        $fetcher->method('fetch')->willReturn($this->response(['products' => $products, 'total' => 2]));
        $repository = new TestSnapshotRepository($this->directory);
        $search = $this->search($fetcher, $http, $repository);
        self::assertNull($search->snapshot('products', 'nomic'));
        self::assertSame(2, $search->index('products', 'nomic')['count']);
        $snapshot = $search->snapshot('products', 'nomic');
        $hash = hash_file('sha256', $snapshot['path']);
        $result = $search->search('products', 'nomic', 'gifts for a techie');
        self::assertSame('laptop', $result['results'][0]['record']['sku']);
        self::assertEqualsWithDelta(0.0, $result['results'][0]['distance'], 1e-9);
        $filtered = $search->search('products', 'nomic', 'gifts for a techie', 'fragrances');
        self::assertCount(1, $filtered['results']);
        self::assertSame('perfume', $filtered['results'][0]['record']['sku']);
        self::assertSame(2, $calls);
        self::assertSame($hash, hash_file('sha256', $snapshot['path']));
        $search->index('products', 'nomic');
        self::assertNotSame($snapshot['path'], $search->snapshot('products', 'nomic')['path']);
        self::assertFileExists($snapshot['path']);
        self::assertSame(2, $calls);
        $repository->snapshots['products/nomic']['dimensions'] = 3072;
        $this->expectExceptionMessage('configuration changed');
        $search->search('products', 'nomic', 'test');
    }

    public function testIncompleteDownloadCannotReplaceCurrentSnapshot(): void
    {
        $fetcher = $this->createStub(PersistentFetcherInterface::class);
        $fetcher->method('fetch')->willReturn($this->response(['products' => [], 'total' => 2]));
        $repository = new TestSnapshotRepository($this->directory);
        $repository->snapshots['products/nomic'] = ['existing' => true];
        $search = $this->search($fetcher, new MockHttpClient(new MockResponse('{"models":[{"name":"nomic-embed-text","digest":"test"}]}')), $repository);
        try {
            $search->index('products', 'nomic');
            self::fail('Incomplete download should fail.');
        } catch (\RuntimeException $exception) {
            self::assertStringContainsString('incomplete', $exception->getMessage());
            self::assertSame(['existing' => true], $repository->snapshots['products/nomic']);
        }
    }

    public function testMoviesUseTheirOwnDatasetAndPreserveMetadata(): void
    {
        file_put_contents($this->directory.'/movies.jsonl', json_encode(['id' => 1, 'title' => 'Moon', 'overview' => 'An astronaut alone.', 'genres' => ['Drama'], 'year' => 2009])."\n");
        $vector = array_fill(0, 768, 0.0);
        $vector[0] = 1.0;
        $http = new MockHttpClient(static fn (string $method, string $url): MockResponse => new MockResponse(str_ends_with($url, '/api/tags') ? '{"models":[{"name":"nomic-embed-text","digest":"test"}]}' : json_encode(['embeddings' => [$vector]])));
        $repository = new TestSnapshotRepository($this->directory);
        $search = $this->search($this->createStub(PersistentFetcherInterface::class), $http, $repository);
        $search->index('movies', 'nomic');
        self::assertNull($search->snapshot('products', 'nomic'));
        $result = $search->search('movies', 'nomic', 'a lonely astronaut');
        self::assertSame('Moon', $result['results'][0]['record']['title']);
        self::assertSame(['Drama'], $result['results'][0]['record']['genres']);
    }

    private function search(PersistentFetcherInterface $fetcher, MockHttpClient $http, TestSnapshotRepository $repository): DuckDbSearch
    {
        $profiles = new EmbeddingProfiles(
            new OpenAiEmbedder($this->createStub(PlatformInterface::class), $this->directory.'/openai.db', 'text-embedding-3-large', 3072),
            new NomicEmbedder($http, $this->directory.'/embeddings.db', 'http://ollama.test'),
            'nomic',
        );

        return new DuckDbSearch(new SearchDatasets($fetcher, $this->directory.'/movies.jsonl'), $profiles, $repository);
    }

    private function response(array $payload): CachedFetchResult
    {
        return new CachedFetchResult(url: SearchDatasets::PRODUCTS_URL, finalUrl: SearchDatasets::PRODUCTS_URL, method: 'GET', statusCode: 200, contentType: 'application/json', contentLength: null, contents: json_encode($payload), errorMessage: null, fetchedAt: time());
    }
}

final class TestSnapshotRepository implements SnapshotRepositoryInterface
{
    public array $snapshots = [];

    public function __construct(private string $directory)
    {
    }

    public function manifest(string $dataset, string $profile): ?array
    {
        return $this->snapshots[$dataset.'/'.$profile] ?? null;
    }

    public function publish(string $path, array $manifest): array
    {
        $manifest['path'] = $this->directory.'/'.bin2hex(random_bytes(6)).'.duckdb';
        copy($path, $manifest['path']);
        $manifest['uri'] = 'test://snapshot';

        return $this->snapshots[$manifest['dataset'].'/'.$manifest['profile']] = $manifest;
    }

    public function open(array $manifest): \PDO
    {
        return new \PDO('duckdb:'.$manifest['path'], null, null, [\PDO::DUCKDB_ATTR_CONFIG => ['access_mode' => 'read_only']]);
    }
}
