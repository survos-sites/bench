<?php

declare(strict_types=1);

namespace App\Search;

use Survos\DuckDbBundle\NamedSources;
use Symfony\AI\Agent\Toolbox\Attribute\AsTool;
use Symfony\Contracts\Service\ResetInterface;

#[AsTool('search_movie_snapshot', 'Search the fixed early-2025 movie snapshot. Use query for semantic themes, title for a literal title substring, director/genre/language/year bounds for metadata filters. minVotes is TMDB vote count. Returns at most 8 movies, never embeddings. Keywords and MPAA certification are unavailable.', method: 'search')]
#[AsTool('get_snapshot_movie', 'Read a movie from the snapshot by its TMDB id, including overview, cast and source links.', method: 'get')]
#[AsTool('movie_snapshot_stats', 'Count movies and summarize the fixed snapshot by genre, language, release decade or status. Counts describe this snapshot, not current TMDB.', method: 'stats')]
final class MovieSnapshotCatalog implements ResetInterface
{
    private ?\PDO $db = null;
    private array $sources = [];
    private array $calls = [];

    public function __construct(private readonly NamedSources $connections, private readonly NomicEmbedder $embedder) {}

    public function reset(): void
    {
        $this->sources = $this->calls = [];
        $this->db = null;
    }

    public function sources(): array { return array_values($this->sources); }
    public function calls(): array { return $this->calls; }

    private function connection(): \PDO
    {
        if (null === $this->db) {
            $this->db = $this->connections->open('movie_snapshot');
            $this->db->exec("SET threads=4; SET memory_limit='4GB'");
        }
        return $this->db;
    }

    public function search(string $query = '', string $title = '', string $director = '', string $genre = '', string $language = '', ?int $yearFrom = null, ?int $yearTo = null, int $minVotes = 0, int $limit = 6): array
    {
        $start = microtime(true);
        $limit = max(1, min(8, $limit));
        $where = ['true'];
        $params = [];
        foreach (['title' => $title, 'director' => $director, 'genres' => $genre] as $column => $value) {
            if ('' !== trim($value)) {
                $where[] = 'contains(lower('.$column.'), lower(?))';
                $params[] = mb_substr(trim($value), 0, 200);
            }
        }
        if ('' !== $language) { $where[] = 'original_language = ?'; $params[] = $language; }
        if (null !== $yearFrom) { $where[] = 'year(try_cast(release_date AS DATE)) >= ?'; $params[] = $yearFrom; }
        if (null !== $yearTo) { $where[] = 'year(try_cast(release_date AS DATE)) <= ?'; $params[] = $yearTo; }
        if ($minVotes > 0) { $where[] = 'vote_count >= ?'; $params[] = $minVotes; }
        $score = 'NULL::FLOAT';
        $order = 'vote_count DESC NULLS LAST, id';
        if ('' !== trim($query)) {
            $vector = $this->embedder->embed([mb_substr(trim($query), 0, 1000)], $this->embedder->digest(), query: true)[0];
            $score = '1 - array_cosine_distance(embedding, CAST(? AS FLOAT[768]))';
            array_unshift($params, json_encode($vector, JSON_THROW_ON_ERROR));
            $where[] = 'embedding IS NOT NULL';
            $order = 'similarity DESC, id';
        }
        $sql = 'SELECT id, title, release_date, genres, director, original_language, overview, vote_average, vote_count, imdb_id, '.$score.' AS similarity FROM movies WHERE '.implode(' AND ', $where).' ORDER BY '.$order.' LIMIT '.$limit;
        $db = $this->connection();
        $queryStart = microtime(true);
        $statement = $db->prepare($sql);
        $statement->execute($params);
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $queryMs = round((microtime(true)-$queryStart)*1000, 1);
        $this->calls[] = ['tool'=>'search_movie_snapshot', 'results'=>count($rows), 'query_ms'=>$queryMs, 'total_ms'=>round((microtime(true)-$start)*1000, 1)];
        return ['snapshot'=>'early 2025; not current', 'movies'=>$this->remember($rows), 'query_ms'=>$queryMs];
    }

    public function get(int $id): array
    {
        $statement = $this->connection()->prepare('SELECT * EXCLUDE (embedding) FROM movies WHERE id = ?');
        $statement->execute([$id]);
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->calls[] = ['tool'=>'get_snapshot_movie', 'results'=>count($rows)];
        return ['movies'=>$this->remember($rows), 'snapshot'=>'early 2025'];
    }

    public function stats(string $groupBy = 'language'): array
    {
        $expression = match ($groupBy) {
            'language' => "coalesce(original_language, 'Unknown')",
            'status' => "coalesce(status, 'Unknown')",
            'decade' => "coalesce(cast(floor(year(try_cast(release_date AS DATE))/10)*10 AS VARCHAR), 'Unknown')",
            'genre' => "trim(unnest(string_split(coalesce(genres, 'Unknown'), ',')))",
            default => throw new \InvalidArgumentException('Choose language, status, decade or genre.'),
        };
        $db = $this->connection();
        $total = (int) $db->query('SELECT count(*) FROM movies')->fetchColumn();
        $rows = $db->query('SELECT '.$expression.' AS label, count(*) AS movies FROM movies GROUP BY label ORDER BY movies DESC, label LIMIT 20')->fetchAll(\PDO::FETCH_ASSOC);
        $this->calls[] = ['tool'=>'movie_snapshot_stats', 'results'=>count($rows)];
        return ['snapshot'=>'early 2025', 'total_movies'=>$total, 'group_by'=>$groupBy, 'top_20'=>$rows, 'note'=>'Genres are multi-valued. Group totals can exceed the number of movies.'];
    }

    private function remember(array $rows): array
    {
        foreach ($rows as &$row) {
            $row['source_url'] = 'https://www.themoviedb.org/movie/'.$row['id'];
            $this->sources[(string) $row['id']] = ['id'=>$row['id'], 'title'=>$row['title'], 'release_date'=>$row['release_date'], 'url'=>$row['source_url']];
            if (isset($row['overview'])) { $row['overview'] = mb_substr($row['overview'], 0, 2400); }
        }
        return $rows;
    }
}
