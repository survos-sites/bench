<?php
declare(strict_types=1);
namespace App\Tests;

use App\Search\MovieSnapshotCatalog;
use App\Search\NomicEmbedder;
use PHPUnit\Framework\TestCase;
use Survos\DuckDbBundle\ConnectionFactory;
use Survos\DuckDbBundle\NamedSources;
use Symfony\Component\HttpClient\MockHttpClient;

final class MovieSnapshotCatalogTest extends TestCase
{
    public function testFiltersSourcesStatsAndParameterBinding(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'movie-test-');
        unlink($file);
        $db = new \PDO('duckdb:'.$file);
        $db->exec('CREATE TABLE movies(id BIGINT, title VARCHAR, release_date VARCHAR, genres VARCHAR, director VARCHAR, original_language VARCHAR, overview VARCHAR, vote_average FLOAT, vote_count INT, imdb_id VARCHAR)');
        $db->exec("INSERT INTO movies VALUES (346, 'Seven Samurai', '1954-04-26', 'Drama, Action', 'Akira Kurosawa', 'ja', 'Village defenders', 8.4, 3000, 'tt0047478'), (2, 'Ariel', '1988-10-21', 'Drama', 'Aki Kaurismäki', 'fi', 'A Finnish story', 7.1, 335, 'tt0094675')");
        $db->exec('ALTER TABLE movies ADD COLUMN embedding FLOAT[768]');
        $db = null;
        try {
            $embedder = new NomicEmbedder(new MockHttpClient(), $file.'.cache', 'http://unused.invalid');
            $catalog = new MovieSnapshotCatalog(new NamedSources(new ConnectionFactory(), ['movie_snapshot'=>['uri'=>$file]]), $embedder);
            $result = $catalog->search(director:'Kurosawa', yearFrom:1950, yearTo:1959);
            self::assertSame([346], array_column($result['movies'], 'id'));
            self::assertSame('https://www.themoviedb.org/movie/346', $catalog->sources()[0]['url']);
            self::assertSame([], $catalog->search(title:"' OR true --")['movies']);
            self::assertSame([], $catalog->search(director:'Kurosawa', yearFrom:1960)['movies']);
            self::assertSame('Ariel', $catalog->get(2)['movies'][0]['title']);
            self::assertSame(2, $catalog->stats()['total_movies']);
            self::assertSame('Drama', $catalog->stats('genre')['top_20'][0]['label']);
            $catalog->reset();
            self::assertSame([], $catalog->sources());
            self::assertSame([], $catalog->calls());
            $this->expectException(\InvalidArgumentException::class);
            $catalog->stats('embedding; DROP TABLE movies');
        } finally {
            unset($catalog);
            unlink($file);
        }
    }
}
