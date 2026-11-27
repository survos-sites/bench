<?php

declare(strict_types=1);

namespace App\Tests;

use App\Search\MovieSnapshotDocuments;
use PHPUnit\Framework\TestCase;
use Survos\DuckDbBundle\ConnectionFactory;
use Survos\DuckDbBundle\NamedSources;

final class MovieSnapshotDocumentsTest extends TestCase
{
    public function testProjectionPreservesVectorsWithoutEmbeddingCalls(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'movie-projection-');
        unlink($file);
        $db = new \PDO('duckdb:'.$file);
        $db->exec('CREATE TABLE movies(id BIGINT, title VARCHAR, original_title VARCHAR, overview VARCHAR, tagline VARCHAR, director VARCHAR, movie_cast VARCHAR, genres VARCHAR, original_language VARCHAR, status VARCHAR, poster_path VARCHAR, release_date VARCHAR, vote_count INT, vote_average FLOAT, popularity FLOAT, embedding FLOAT[768])');
        $db->exec("INSERT INTO movies (id,title,genres,release_date,vote_count,embedding) VALUES (2,'Ariel','Drama, Comedy, Drama, ','1988-10-21',335,array_resize([1.0],768,0.0)),(3,'Unknown',NULL,'bad date',NULL,array_resize([0.0,1.0],768,0.0))");
        $db = null;
        try {
            $provider = new MovieSnapshotDocuments(new NamedSources(new ConnectionFactory(), ['movie_snapshot'=>['uri'=>$file]]));
            $rows = iterator_to_array($provider());
            self::assertCount(2, $rows);
            self::assertSame([2,3], array_column($rows, 'id'));
            self::assertSame(['Drama','Comedy'], $rows[0]['genres']);
            self::assertSame(1988, $rows[0]['year']);
            self::assertSame(335, $rows[0]['vote_count']);
            self::assertNull($rows[1]['year']);
            self::assertNull($rows[1]['vote_count']);
            self::assertSame([], $rows[1]['genres']);
            self::assertSame('hf-early-2025', $rows[0]['snapshot']);
            self::assertCount(768, $rows[0]['embedding']);
            self::assertEquals(1.0, $rows[0]['embedding'][0]);
            self::assertEquals(0.0, $rows[0]['embedding'][767]);
        } finally {
            unlink($file);
        }
    }
}
