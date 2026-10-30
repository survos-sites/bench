<?php

declare(strict_types=1);

namespace App\Search;

use Survos\Ai\DuckDb\Store;
use Symfony\AI\Platform\Vector\Vector;
use Symfony\AI\Store\Document\Metadata;
use Symfony\AI\Store\Document\VectorDocument;
use Symfony\AI\Store\Query\VectorQuery;

final class DuckDbSearch
{
    public function __construct(
        private readonly SearchDatasets $datasets,
        private readonly EmbeddingProfiles $profiles,
        private readonly SnapshotRepositoryInterface $repository,
    ) {
    }

    public function snapshot(string $dataset, string $profile): ?array
    {
        $this->profiles->get($profile);
        if (!in_array($dataset, ['products', 'movies'], true)) {
            throw new \InvalidArgumentException('Unknown dataset.');
        }

        return $this->repository->manifest($dataset, $profile);
    }

    public function index(string $dataset, string $profile, bool $refresh = false, ?callable $progress = null): array
    {
        $embedder = $this->profiles->get($profile);
        $digest = $embedder->digest();
        $input = $this->datasets->load($dataset, $refresh);
        $records = $input['records'];
        $ids = [];
        foreach ($records as $record) {
            $id = (string) ($record['products' === $dataset ? 'sku' : 'id'] ?? '');
            if ('' === $id || isset($ids[$id])) {
                throw new \RuntimeException('Dataset identifiers must be non-empty and unique.');
            }
            $ids[$id] = true;
        }
        // Only the publisher needs temporary writable storage, never the web reader.
        $path = sys_get_temp_dir().'/bench-'.bin2hex(random_bytes(12)).'.duckdb';
        $started = microtime(true);
        try {
            $pdo = new \PDO('duckdb:'.$path);
            $store = new Store($pdo, $dataset, $embedder->dimensions());
            $store->setup();
            $categories = [];
            foreach (array_chunk($records, 'nomic' === $profile ? 16 : 64) as $batch) {
                $texts = array_map(static fn (array $row): string => SearchDatasets::text($dataset, $row), $batch);
                $vectors = $embedder->embed($texts, $digest);
                $documents = [];
                foreach ($batch as $i => $record) {
                    $id = (string) $record['products' === $dataset ? 'sku' : 'id'];
                    $documents[] = new VectorDocument($id, new Vector($vectors[$i]), new Metadata($record + ['_text' => $texts[$i]]));
                    if ('products' === $dataset) {
                        $categories[$record['category']] = true;
                    }
                }
                $store->add($documents);
                if (null !== $progress) {
                    $progress(count($store), count($records));
                }
            }
            $version = $pdo->query('SELECT version()')->fetchColumn();
            $pdo->exec('CHECKPOINT');
            unset($store, $pdo);
            $categories = array_keys($categories);
            sort($categories);
            $manifest = $this->repository->publish($path, [
                'dataset' => $dataset, 'profile' => $profile, 'table' => $dataset,
                'count' => count($records), 'model' => $embedder->model(), 'dimensions' => $embedder->dimensions(),
                'digest' => $digest, 'created' => gmdate(DATE_ATOM), 'categories' => $categories,
                'source' => $input['source'], 'duckdbVersion' => $version, 'textVersion' => 1,
            ]);
        } finally {
            unset($store, $pdo);
            foreach ([$path, $path.'.wal'] as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }

        return ['count' => count($records), 'seconds' => microtime(true) - $started, 'fetchCached' => $input['fetchCached'], 'manifest' => $manifest];
    }

    public function search(string $dataset, string $profile, string $query, ?string $category = null, ?array $snapshot = null): array
    {
        $started = microtime(true);
        $snapshot ??= $this->snapshot($dataset, $profile) ?? throw new \RuntimeException('Build this dataset and model snapshot first.');
        $embedder = $this->profiles->get($profile);
        $digest = $embedder->digest();
        if ($snapshot['digest'] !== $digest || $snapshot['dimensions'] !== $embedder->dimensions() || $snapshot['model'] !== $embedder->model()) {
            throw new \RuntimeException('Embedding configuration changed since indexing. Rebuild this profile before searching.');
        }
        $vector = $embedder->embed([$query], $digest, query: true)[0];
        $searchStarted = microtime(true);
        $store = new Store($this->repository->open($snapshot), $snapshot['table'], $snapshot['dimensions']);
        $results = [];
        foreach ($store->query(new VectorQuery(new Vector($vector)), ['limit' => 12, 'filter' => 'products' === $dataset && null !== $category && '' !== $category ? ['category' => $category] : []]) as $document) {
            $results[] = ['record' => $document->getMetadata()->getArrayCopy(), 'distance' => $document->getScore()];
        }

        return ['results' => $results, 'searchMs' => (microtime(true) - $searchStarted) * 1000, 'totalMs' => (microtime(true) - $started) * 1000];
    }
}
