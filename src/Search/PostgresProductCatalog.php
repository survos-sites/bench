<?php

declare(strict_types=1);

namespace App\Search;

use Doctrine\DBAL\Connection;
use Survos\SearchBundle\Contract\QueryEmbedderInterface;
use Survos\SearchBundle\Search\VectorSearch;
use Survos\SearchBundle\Search\EmbeddingProfile;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/** PostgreSQL projection of a published product snapshot. Import never calls an embedding API. */
final readonly class PostgresProductCatalog implements QueryEmbedderInterface
{
    public function __construct(
        #[Autowire(service: 'doctrine.dbal.products_connection')] private Connection $connection,
        private LocalProductCatalog $source,
        private EmbeddingProfiles $profiles,
    ) {}

    public function manifest(): ?array
    {
        if (null === $this->connection->fetchOne("SELECT to_regclass('product_search_snapshot')")) {
            return null;
        }
        $json = $this->connection->fetchOne('SELECT manifest FROM product_search_snapshot WHERE id = 1');

        return false === $json ? null : json_decode($json, true, flags: JSON_THROW_ON_ERROR);
    }

    public function import(): int
    {
        $manifest = $this->source->manifest() ?? throw new \RuntimeException('Build the local products DuckDB first.');
        $db = $this->source->open($manifest);
        $rows = $db->query('SELECT id, CAST(metadata AS VARCHAR) AS metadata, CAST(embedding AS VARCHAR) AS vector FROM products ORDER BY id')->fetchAll(\PDO::FETCH_ASSOC);
        if (count($rows) !== $manifest['count'] || [] === $rows) {
            throw new \RuntimeException('Source snapshot record count does not match its manifest.');
        }
        foreach ($rows as &$row) {
            $metadata = json_decode($row['metadata'], true, flags: JSON_THROW_ON_ERROR);
            $text = $metadata['_text'] ?? '';
            if ($text !== SearchDatasets::text('products', $metadata)) {
                throw new \RuntimeException('Stored embedding text differs from the current product text; rebuild the source snapshot.');
            }
            $vector = json_decode($row['vector'] ?? 'null', true, flags: JSON_THROW_ON_ERROR);
            VectorSearch::validate($vector ?? [], $manifest['dimensions']);
            $row['contentHash'] = hash('sha256', $text);
            $row['fingerprint'] = $this->profile($manifest)->fingerprint($text);
            $row['text'] = $text;
        }
        unset($row);
        $this->connection->transactional(function () use ($manifest, $rows): void {
            $this->connection->executeStatement('CREATE TABLE IF NOT EXISTS product_search (id TEXT PRIMARY KEY, metadata JSONB NOT NULL, search_text TEXT NOT NULL, embedding vector NOT NULL, embedding_json TEXT NOT NULL, content_hash TEXT NOT NULL, embedding_fingerprint TEXT NOT NULL, snapshot_hash TEXT NOT NULL)');
            $this->connection->executeStatement('CREATE TABLE IF NOT EXISTS product_search_snapshot (id INTEGER PRIMARY KEY CHECK (id = 1), manifest JSONB NOT NULL)');
            $sql = 'INSERT INTO product_search VALUES (:id, CAST(:metadata AS jsonb), :text, CAST(:vector AS vector), :vector, :contentHash, :fingerprint, :snapshot) ON CONFLICT (id) DO UPDATE SET metadata = EXCLUDED.metadata, search_text = EXCLUDED.search_text, embedding = EXCLUDED.embedding, embedding_json = EXCLUDED.embedding_json, content_hash = EXCLUDED.content_hash, embedding_fingerprint = EXCLUDED.embedding_fingerprint, snapshot_hash = EXCLUDED.snapshot_hash WHERE product_search.metadata IS DISTINCT FROM EXCLUDED.metadata OR product_search.embedding_fingerprint <> EXCLUDED.embedding_fingerprint OR product_search.snapshot_hash <> EXCLUDED.snapshot_hash';
            foreach ($rows as $row) {
                $this->connection->executeStatement($sql, $row + ['snapshot' => $manifest['file']]);
            }
            $this->connection->executeStatement('DELETE FROM product_search WHERE snapshot_hash <> ?', [$manifest['file']]);
            $this->connection->executeStatement('INSERT INTO product_search_snapshot VALUES (1, CAST(? AS jsonb)) ON CONFLICT (id) DO UPDATE SET manifest = EXCLUDED.manifest', [json_encode($manifest, JSON_THROW_ON_ERROR)]);
        });

        return count($rows);
    }

    private function profile(array $manifest): EmbeddingProfile
    {
        return new EmbeddingProfile($manifest['model'], $manifest['dimensions'], $manifest['digest'], (string) $manifest['textVersion']);
    }

    public function embedQuery(string $text): array
    {
        $manifest = $this->manifest() ?? throw new \RuntimeException('Run app:products:postgres:import first.');
        $embedder = $this->profiles->get($manifest['profile']);
        if ($embedder->digest() !== $manifest['digest'] || $embedder->model() !== $manifest['model'] || $embedder->dimensions() !== $manifest['dimensions']) {
            throw new \RuntimeException('Embedding profile differs from the PostgreSQL snapshot. Import a matching snapshot before searching.');
        }

        return $embedder->embed([$text], $manifest['digest'], query: true)[0];
    }

    /** Propagate validated stored vectors, never regenerate them during Elasticsearch indexing. */
    public function elasticDocuments(): \Generator
    {
        $manifest = $this->manifest() ?? throw new \RuntimeException('Import the PostgreSQL products first.');
        foreach ($this->connection->iterateAssociative('SELECT * FROM product_search ORDER BY id') as $row) {
            $metadata = json_decode($row['metadata'], true, flags: JSON_THROW_ON_ERROR);
            $text = SearchDatasets::text('products', $metadata);
            if ($row['snapshot_hash'] !== $manifest['file'] || $row['content_hash'] !== hash('sha256', $text) || $row['embedding_fingerprint'] !== $this->profile($manifest)->fingerprint($text)) {
                throw new \RuntimeException('Product embedding provenance mismatch; import the updated snapshot before exporting.');
            }
            $vector = json_decode($row['embedding_json'], true, flags: JSON_THROW_ON_ERROR);
            VectorSearch::validate($vector, $manifest['dimensions']);
            yield [
                'id' => $row['id'], 'title' => $metadata['title'], 'description' => $metadata['description'],
                'category' => $metadata['category'], 'brand' => $metadata['brand'] ?? '', 'price' => (float) $metadata['price'],
                'thumbnail' => $metadata['thumbnail'] ?? '', 'embedding' => $vector,
                'embeddingFingerprint' => $row['embedding_fingerprint'], 'contentHash' => $row['content_hash'],
                'embeddingProfile' => $manifest['digest'], 'embeddingModel' => $manifest['model'],
                'embeddingDimensions' => $manifest['dimensions'], 'textVersion' => $manifest['textVersion'],
            ];
        }
    }
}
