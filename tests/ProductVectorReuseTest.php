<?php

declare(strict_types=1);

namespace App\Tests;

use App\Search\EmbeddingProfiles;
use App\Search\LocalProductCatalog;
use App\Search\NomicEmbedder;
use App\Search\OpenAiEmbedder;
use App\Search\PostgresProductCatalog;
use App\Search\SearchDatasets;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use PHPUnit\Framework\TestCase;
use Survos\SearchBundle\Search\VectorSearch;
use Symfony\AI\Platform\PlatformInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\MockHttpClient;

final class ProductVectorReuseTest extends TestCase
{
    private string $directory;
    private Connection $db;
    private PostgresProductCatalog $catalog;

    protected function setUp(): void
    {
        $url = getenv('POSTGRES_VECTOR_TEST_URL');
        if (!$url) { self::markTestSkipped('Set POSTGRES_VECTOR_TEST_URL to a local pgvector database.'); }
        $this->directory = sys_get_temp_dir().'/bench-vector-reuse-'.bin2hex(random_bytes(6));
        mkdir($this->directory);
        $this->db = DriverManager::getConnection((new DsnParser(['postgresql' => 'pdo_pgsql']))->parse($url));
        // Temporary tables shadow real demo tables on this connection only.
        $this->db->executeStatement('CREATE TEMPORARY TABLE product_search (id TEXT PRIMARY KEY, metadata JSONB NOT NULL, search_text TEXT NOT NULL, embedding vector NOT NULL, embedding_json TEXT NOT NULL, content_hash TEXT NOT NULL, embedding_fingerprint TEXT NOT NULL, snapshot_hash TEXT NOT NULL)');
        $this->db->executeStatement('CREATE TEMPORARY TABLE product_search_snapshot (id INTEGER PRIMARY KEY, manifest JSONB NOT NULL)');
        $platform = $this->createMock(PlatformInterface::class);
        $platform->expects(self::never())->method('invoke');
        $profiles = new EmbeddingProfiles(new OpenAiEmbedder($platform, $this->directory.'/cache.db', 'test-model', 2), new NomicEmbedder(new MockHttpClient(), $this->directory.'/nomic.db', 'http://unused.invalid'), 'openai');
        $source = new LocalProductCatalog($this->directory, $profiles);
        $file = $this->directory.'/source.duckdb';
        $duck = new \PDO('duckdb:'.$file);
        $duck->exec('CREATE TABLE products (id VARCHAR, metadata JSON, embedding DOUBLE[2])');
        $row = ['title' => 'Wireless speaker', 'description' => 'Portable sound for a tech gift', 'category' => 'mobile-accessories', 'price' => 49.99, 'sku' => 'speaker'];
        $row['_text'] = SearchDatasets::text('products', $row);
        $duck->prepare('INSERT INTO products VALUES (?, CAST(? AS JSON), [0.8, 0.6])')->execute(['speaker', json_encode($row)]);
        $duck->exec('CHECKPOINT');
        unset($duck);
        $source->publish($file, ['profile' => 'openai', 'dimensions' => 2, 'model' => 'test-model', 'digest' => 'openai:test-model:2:raw-text-v1', 'count' => 1, 'textVersion' => 1]);
        $this->catalog = new PostgresProductCatalog($this->db, $source, $profiles);
    }

    protected function tearDown(): void
    {
        if (isset($this->db)) { $this->db->close(); }
        if (isset($this->directory)) { (new Filesystem())->remove($this->directory); }
    }

    public function testImportAndElasticsearchProjectionReuseStoredVectorsWithoutProviderCalls(): void
    {
        self::assertSame(1, $this->catalog->import());
        self::assertSame(1, $this->catalog->import(), 'Reimport is idempotent.');
        self::assertSame(1, (int) $this->db->fetchOne('SELECT count(*) FROM product_search'));
        self::assertSame(2, (int) $this->db->fetchOne('SELECT vector_dims(embedding) FROM product_search'));
        $doc = iterator_to_array($this->catalog->elasticDocuments())[0];
        self::assertSame([0.8, 0.6], $doc['embedding']);
        self::assertSame(2, $doc['embeddingDimensions']);
        self::assertSame('test-model', $doc['embeddingModel']);
        $text = $this->db->fetchOne('SELECT search_text FROM product_search');
        self::assertSame(hash('sha256', $text), $doc['contentHash']);
        self::assertSame((new \Survos\SearchBundle\Search\EmbeddingProfile('test-model', 2, 'openai:test-model:2:raw-text-v1', '1'))->fingerprint($text), $doc['embeddingFingerprint']);
        self::assertNotSame(VectorSearch::fingerprint('openai:test-model:3:raw-text-v1', $text), $doc['embeddingFingerprint']);
        self::assertNotSame(VectorSearch::fingerprint('openai:test-model:2:raw-text-v2', $text), $doc['embeddingFingerprint']);
    }

    public function testChangedContentCannotExportStaleVectors(): void
    {
        $this->catalog->import();
        $this->db->executeStatement("UPDATE product_search SET metadata = jsonb_set(metadata, '{description}', '\"Changed text\"')");
        $this->expectExceptionMessage('provenance mismatch');
        iterator_to_array($this->catalog->elasticDocuments());
    }

    public function testChangedProfileCannotExportStaleVectors(): void
    {
        $this->catalog->import();
        $this->db->executeStatement("UPDATE product_search_snapshot SET manifest = jsonb_set(manifest, '{digest}', '\"other-model\"')");
        $this->expectExceptionMessage('provenance mismatch');
        iterator_to_array($this->catalog->elasticDocuments());
    }
}
