<?php

declare(strict_types=1);

namespace App\Tests;

use App\Search\EmbeddingProfiles;
use App\Search\LocalProductCatalog;
use App\Search\NomicEmbedder;
use App\Search\OpenAiEmbedder;
use PHPUnit\Framework\TestCase;
use Symfony\AI\Platform\PlatformInterface;
use Symfony\Component\HttpClient\MockHttpClient;

final class LocalProductCatalogTest extends TestCase
{
    public function testLocalCapabilityDependsOnStoredVectorsAndConnectionIsReadOnly(): void
    {
        $directory = sys_get_temp_dir().'/bench-local-test-'.bin2hex(random_bytes(8));
        mkdir($directory);
        $profiles = new EmbeddingProfiles(new OpenAiEmbedder($this->createStub(PlatformInterface::class), $directory.'/cache.db', 'test', 2), new NomicEmbedder(new MockHttpClient(), $directory.'/nomic.db', 'http://localhost'), 'openai');
        $catalog = new LocalProductCatalog($directory, $profiles);
        $path = $directory.'/writer.duckdb';
        try {
            self::assertFalse($catalog->hasVectors());
            $db = new \PDO('duckdb:'.$path);
            $db->exec('CREATE TABLE products(id VARCHAR, metadata JSON, embedding DOUBLE[2])');
            $db->exec("INSERT INTO products VALUES ('1', '{\"_text\":\"a wireless speaker\"}', NULL)");
            $db->exec('CHECKPOINT');
            unset($db);
            $catalog->publish($path, ['profile' => 'openai', 'dimensions' => 2]);
            self::assertFalse($catalog->hasVectors());
            $db = new \PDO('duckdb:'.$path);
            $db->exec("PRAGMA drop_fts_index('products')");
            $db->exec('UPDATE products SET embedding = [1.0, 0.0]');
            $db->exec('ALTER TABLE products DROP COLUMN search_text');
            $db->exec('CHECKPOINT');
            unset($db);
            $catalog->publish($path, ['profile' => 'openai', 'dimensions' => 2]);
            self::assertTrue($catalog->hasVectors());
            $reader = $catalog->connect();
            self::assertSame(1, (int) $reader->query("SELECT count(*) FROM products WHERE fts_main_products.match_bm25(id, 'speaker') IS NOT NULL")->fetchColumn());
            $this->expectException(\PDOException::class);
            $reader->exec('DELETE FROM products');
        } finally {
            unset($db, $reader);
            foreach (glob($directory.'/*') as $file) {
                unlink($file);
            }
            rmdir($directory);
        }
    }
}
