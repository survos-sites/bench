<?php

declare(strict_types=1);

namespace App\Search;

use Survos\Ai\DuckDb\Store;
use Survos\FetchBundle\Contract\PersistentFetcherInterface;
use Symfony\AI\Platform\Vector\Vector;
use Symfony\AI\Store\Document\Metadata;
use Symfony\AI\Store\Document\VectorDocument;
use Symfony\AI\Store\Query\VectorQuery;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class DuckDbProductSearch
{
    public const SOURCE = 'https://dummyjson.com/products?limit=0';

    public function __construct(
        private readonly PersistentFetcherInterface $fetcher,
        private readonly NomicEmbedder $embedder,
        #[Autowire('%kernel.project_dir%/var/data/duckdb-products')]
        private readonly string $directory,
    ) {
    }

    /** @return array{file: string, count: int, model: string, digest: string, created: string, categories: list<string>}|null */
    public function snapshot(): ?array
    {
        $path = $this->directory.'/current.json';
        return is_file($path) ? json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR) : null;
    }

    /** @return array{count: int, seconds: float, fetchCached: bool} */
    public function index(bool $refresh = false, ?callable $progress = null): array
    {
        $digest = $this->embedder->digest();
        $cached = !$refresh && $this->fetcher->isCached(self::SOURCE);
        $response = $this->fetcher->fetch(self::SOURCE, ['ttl' => 0, 'force_fetch' => $refresh, 'timeout' => 30.0]);
        if (!$response->isOkay() || null === $response->contents) {
            $this->fetcher->forget(self::SOURCE);
            throw new \RuntimeException('DummyJSON product download failed.');
        }
        $payload = json_decode($response->contents, true, flags: JSON_THROW_ON_ERROR);
        $products = $payload['products'] ?? [];
        if ([] === $products || count($products) !== ($payload['total'] ?? null)) {
            throw new \RuntimeException('The product download is empty or incomplete.');
        }
        if (!is_dir($this->directory) && !mkdir($this->directory, 0775, true) && !is_dir($this->directory)) {
            throw new \RuntimeException('Cannot create the snapshot directory.');
        }
        file_put_contents($this->directory.'/products.json', $response->contents);
        $filename = 'products-'.gmdate('Ymd-His').'-'.bin2hex(random_bytes(4)).'.duckdb';
        $path = $this->directory.'/'.$filename;
        $started = microtime(true);
        $published = false;
        try {
            $store = new Store(new \PDO('duckdb:'.$path), 'products', NomicEmbedder::DIMENSIONS);
            $store->setup();
            $categories = [];
            foreach (array_chunk($products, 16) as $batch) {
                $texts = array_map(self::text(...), $batch);
                $vectors = $this->embedder->embed($texts, $digest);
                $documents = [];
                foreach ($batch as $i => $product) {
                    $documents[] = new VectorDocument($product['sku'], new Vector($vectors[$i]), new Metadata($product + ['_text' => $texts[$i]]));
                    $categories[$product['category']] = true;
                }
                $store->add($documents);
                if (null !== $progress) {
                    $progress(count($store), count($products));
                }
            }
            if (count($store) !== count($products)) {
                throw new \RuntimeException('Product SKUs are not unique.');
            }
            unset($store);
            $categories = array_keys($categories);
            sort($categories);
            $manifest = ['file' => $filename, 'count' => count($products), 'model' => NomicEmbedder::MODEL, 'digest' => $digest, 'created' => gmdate(DATE_ATOM), 'categories' => $categories];
            $temporary = $this->directory.'/current-'.bin2hex(random_bytes(4)).'.json';
            if (false === file_put_contents($temporary, json_encode($manifest, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)) || !rename($temporary, $this->directory.'/current.json')) {
                throw new \RuntimeException('Cannot publish the completed snapshot.');
            }
            $published = true;
        } finally {
            unset($store);
            if (!$published && is_file($path)) {
                unlink($path);
            }
            if (isset($temporary) && is_file($temporary)) {
                unlink($temporary);
            }
        }
        return ['count' => count($products), 'seconds' => microtime(true) - $started, 'fetchCached' => $cached];
    }

    /** @return array{results: list<array{product: array<string, mixed>, distance: float|null}>, searchMs: float, totalMs: float} */
    public function search(string $query, ?string $category = null): array
    {
        $started = microtime(true);
        $snapshot = $this->snapshot() ?? throw new \RuntimeException('Build the product snapshot first.');
        $digest = $this->embedder->digest();
        if ($snapshot['digest'] !== $digest) {
            throw new \RuntimeException('Nomic has changed since indexing. Rebuild the product snapshot.');
        }
        $vector = $this->embedder->embed([$query], $digest, query: true)[0];
        $pdo = new \PDO('duckdb:'.$this->directory.'/'.$snapshot['file'], null, null, [\PDO::DUCKDB_ATTR_CONFIG => ['access_mode' => 'read_only']]);
        $store = new Store($pdo, 'products', NomicEmbedder::DIMENSIONS);
        $searchStarted = microtime(true);
        $results = [];
        foreach ($store->query(new VectorQuery(new Vector($vector)), ['limit' => 12, 'filter' => null === $category || '' === $category ? [] : ['category' => $category]]) as $document) {
            $results[] = ['product' => $document->getMetadata()->getArrayCopy(), 'distance' => $document->getScore()];
        }
        return ['results' => $results, 'searchMs' => (microtime(true) - $searchStarted) * 1000, 'totalMs' => (microtime(true) - $started) * 1000];
    }

    /** @param array<string, mixed> $product */
    private static function text(array $product): string
    {
        return implode("\n", [$product['title'], $product['description'], 'Brand: '.($product['brand'] ?? ''), 'Category: '.$product['category'], 'Tags: '.implode(', ', $product['tags'] ?? [])]);
    }
}
