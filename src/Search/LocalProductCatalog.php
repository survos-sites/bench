<?php

declare(strict_types=1);

namespace App\Search;

use Survos\SearchBundle\Adapter\DuckDb\DuckDbConnectionProviderInterface;
use Survos\SearchBundle\Adapter\DuckDb\DuckDbQueryEmbedderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/** Local development snapshot, independent of S3 and Doctrine. */
final class LocalProductCatalog implements SnapshotRepositoryInterface, DuckDbConnectionProviderInterface, DuckDbQueryEmbedderInterface
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/var/data/product-search')]
        private readonly string $directory,
        private readonly EmbeddingProfiles $profiles,
    ) {
    }

    public function name(): string
    {
        return 'local-products';
    }

    public function manifest(string $dataset = 'products', string $profile = 'openai'): ?array
    {
        $path = $this->directory.'/current.json';

        return is_file($path) ? json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR) : null;
    }

    public function publish(string $path, array $manifest): array
    {
        $db = new \PDO('duckdb:'.$path);
        $db->exec('LOAD fts');
        $db->exec('ALTER TABLE products ADD COLUMN search_text VARCHAR');
        $db->exec("UPDATE products SET search_text = metadata->>'_text'");
        $db->exec("PRAGMA create_fts_index('products', 'id', 'search_text')");
        $db->exec('CHECKPOINT');
        unset($db);
        if (!is_dir($this->directory)) {
            mkdir($this->directory, 0775, true);
        }
        $manifest['file'] = hash_file('sha256', $path).'.duckdb';
        if (!copy($path, $this->directory.'/'.$manifest['file'])) {
            throw new \RuntimeException('Cannot publish local product database.');
        }
        file_put_contents($this->directory.'/current.json.tmp', json_encode($manifest, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
        rename($this->directory.'/current.json.tmp', $this->directory.'/current.json');

        return $manifest;
    }

    public function open(array $manifest): \PDO
    {
        if (!preg_match('/^[a-f0-9]{64}\.duckdb$/D', $manifest['file'] ?? '')) {
            throw new \RuntimeException('Invalid local product snapshot.');
        }
        $db = new \PDO('duckdb:'.$this->directory.'/'.$manifest['file'], null, null, [\PDO::DUCKDB_ATTR_CONFIG => ['access_mode' => 'read_only']]);
        $db->exec('LOAD fts');

        return $db;
    }

    public function connect(array $options = []): \PDO
    {
        return $this->open($this->manifest() ?? throw new \RuntimeException('Run app:duckdb:products:local first.'));
    }

    public function hasVectors(): bool
    {
        if (null === $this->manifest()) {
            return false;
        }
        $db = $this->connect();
        $columns = $db->query('DESCRIBE products')->fetchAll(\PDO::FETCH_COLUMN);

        return in_array('embedding', $columns, true) && (bool) $db->query('SELECT EXISTS(SELECT 1 FROM products WHERE embedding IS NOT NULL)')->fetchColumn();
    }

    public function embedQuery(string $text): array
    {
        $manifest = $this->manifest() ?? throw new \RuntimeException('Missing local snapshot.');
        $embedder = $this->profiles->get($manifest['profile']);
        if ($embedder->model() !== $manifest['model'] || $embedder->dimensions() !== $manifest['dimensions'] || $embedder->digest() !== $manifest['digest']) {
            throw new \RuntimeException('Embedding profile differs from the local snapshot. Rebuild it before searching.');
        }

        return $embedder->embed([$text], $manifest['digest'], query: true)[0];
    }
}
