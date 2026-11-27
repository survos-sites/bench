<?php

declare(strict_types=1);

namespace App\Search;

use Symfony\AI\Agent\Toolbox\Attribute\AsTool;
use Symfony\Contracts\Service\ResetInterface;

#[AsTool('search_movie_snapshot', 'Search the fixed early-2025 movie snapshot. Use query for semantic themes; title and director for literal substrings; genre/language/year bounds for filters. Returns at most 8 movies, never vectors. No keywords or MPAA certification.', method: 'search')]
#[AsTool('get_snapshot_movie', 'Read a snapshot movie by its TMDB ID, with source link.', method: 'get')]
#[AsTool('movie_snapshot_stats', 'Count snapshot movies, grouped by language, status, genre or release decade.', method: 'stats')]
final class MovieElasticCatalog implements ResetInterface
{
    private array $sources = [];
    private array $calls = [];

    public function __construct(private readonly MovieElasticClient $client, private readonly NomicEmbedder $embedder) {}

    public function reset(): void { $this->sources = $this->calls = []; }
    public function sources(): array { return array_values($this->sources); }
    public function calls(): array { return $this->calls; }

    public static function filters(string $title, string $director, string $genre, string $language, ?int $yearFrom, ?int $yearTo, int $minVotes): array
    {
        $filters = [];
        foreach (['title'=>$title, 'director'=>$director] as $field=>$value) {
            if ('' !== trim($value)) {
                // Escape wildcard syntax: user filters remain literal substrings.
                $value = str_replace(['\\', '*', '?'], ['\\\\', '\\*', '\\?'], mb_substr(trim($value), 0, 200));
                $filters[] = ['wildcard'=>[$field.'.literal'=>['value'=>'*'.$value.'*', 'case_insensitive'=>true]]];
            }
        }
        if ('' !== trim($genre)) { $filters[] = ['term'=>['genres'=>['value'=>trim($genre),'case_insensitive'=>true]]]; }
        if ('' !== $language) { $filters[] = ['term'=>['original_language'=>$language]]; }
        $year = [];
        if (null !== $yearFrom) { $year['gte'] = $yearFrom; }
        if (null !== $yearTo) { $year['lte'] = $yearTo; }
        if ($year) { $filters[] = ['range'=>['year'=>$year]]; }
        if ($minVotes > 0) { $filters[] = ['range'=>['vote_count'=>['gte'=>$minVotes]]]; }
        return $filters;
    }

    /**
     * @param string $query Semantic themes or plots only; empty for director/title/date lookups.
     * @param string $title A movie title substring, never a person's name unless it is the requested movie title. Empty when no title was requested.
     * @param string $director Director name substring; do not also copy it into title.
     * @param string $genre Genre explicitly requested by the user; otherwise empty.
     * @param string $language Two-letter original-language code such as ja or en, only when requested; otherwise empty.
     * @param int|null $yearFrom Inclusive first release year, or null when unspecified.
     * @param int|null $yearTo Inclusive last release year, or null when unspecified.
     * @param int $minVotes Minimum TMDB votes; zero unless the user requested a popularity filter.
     * @param int $limit Maximum results, capped at eight.
     */
    public function search(string $query = '', string $title = '', string $director = '', string $genre = '', string $language = '', ?int $yearFrom = null, ?int $yearTo = null, int $minVotes = 0, int $limit = 6): array
    {
        $start = microtime(true);
        $vector = null;
        if ('' !== trim($query)) {
            $vector = $this->embedder->embed([mb_substr(trim($query), 0, 1000)], $this->embedder->digest(), query:true)[0];
        }
        $filters = self::filters($title, $director, $genre, $language, $yearFrom, $yearTo, $minVotes);
        $queryStart = microtime(true);
        $result = $this->retrieve($vector, $filters, max(1,min(8,$limit)));
        $this->calls[] = ['tool'=>'search_movie_snapshot', 'backend'=>'elasticsearch', 'semantic_query'=>$query, 'filters'=>$filters, 'results'=>count($result['movies']), 'query_ms'=>round((microtime(true)-$queryStart)*1000,1), 'engine_ms'=>$result['engine_ms'], 'total_ms'=>round((microtime(true)-$start)*1000,1)];
        $result['movies'] = $this->remember($result['movies']);
        return $result + [
            'snapshot'=>'early 2025; not current',
            'filters_used'=>compact('title', 'director', 'genre', 'language', 'yearFrom', 'yearTo', 'minVotes'),
            'note'=>[] === $result['movies'] ? 'No rows matched this combination. Check for accidentally inferred filters before claiming the catalog has no matches.' : null,
        ];
    }

    /** Shared by Ask and the exact-DuckDB benchmark; takes a query vector, never embeds documents. */
    public function retrieve(?array $vector, array $filters, int $limit = 8, int $candidates = 1000): array
    {
        $body = ['size'=>$limit, '_source'=>['excludes'=>['embedding']], 'track_total_hits'=>false];
        $filter = $filters ? ['bool'=>['filter'=>$filters]] : ['match_all'=>(object)[]];
        if (null !== $vector) {
            if (count($vector) !== 768) { throw new \InvalidArgumentException('Expected 768 query dimensions.'); }
            $body['knn'] = ['field'=>'embedding', 'query_vector'=>$vector, 'k'=>$limit, 'num_candidates'=>max($limit,$candidates), 'filter'=>$filter];
        } else {
            $body['query'] = $filter;
            $body['sort'] = [['vote_count'=>['order'=>'desc','missing'=>'_last']], ['id'=>'asc']];
        }
        $result = $this->client->search($body);
        $rows = [];
        foreach ($result['hits']['hits'] as $hit) {
            $row = $hit['_source'];
            // ES cosine scores are (1 + cosine) / 2. Expose the same similarity as DuckDB.
            $row['similarity'] = null === $vector ? null : 2*$hit['_score']-1;
            $rows[] = $row;
        }
        return ['movies'=>$rows, 'engine_ms'=>$result['took']];
    }

    public function get(int $id): array
    {
        $result = $this->client->search(['size'=>1, '_source'=>['excludes'=>['embedding']], 'query'=>['term'=>['id'=>$id]]]);
        $rows = array_column($result['hits']['hits'], '_source');
        $this->calls[] = ['tool'=>'get_snapshot_movie','backend'=>'elasticsearch','results'=>count($rows)];
        return ['movies'=>$this->remember($rows), 'snapshot'=>'early 2025'];
    }

    public function stats(string $groupBy = 'language'): array
    {
        $field = match ($groupBy) { 'language'=>'original_language', 'status'=>'status', 'genre'=>'genres', 'decade'=>'year', default=>throw new \InvalidArgumentException('Choose language, status, decade or genre.') };
        $aggregation = 'decade' === $groupBy
            ? ['terms'=>['script'=>['source'=>"doc['year'].size() == 0 ? 'Unknown' : Long.toString((long)(Math.floor(doc['year'].value / 10.0) * 10))"], 'size'=>20]]
            : ['terms'=>['field'=>$field,'missing'=>'Unknown','size'=>20]];
        $result = $this->client->search(['size'=>0,'track_total_hits'=>true,'aggs'=>['groups'=>$aggregation]]);
        $rows = array_map(static fn(array $bucket): array => ['label'=>(string)$bucket['key'],'movies'=>$bucket['doc_count']],$result['aggregations']['groups']['buckets']);
        $this->calls[] = ['tool'=>'movie_snapshot_stats','backend'=>'elasticsearch','results'=>count($rows)];
        return ['snapshot'=>'early 2025','total_movies'=>$result['hits']['total']['value'],'group_by'=>$groupBy,'top_20'=>$rows,'note'=>'Genres are multi-valued; totals can exceed the movie count.'];
    }

    private function remember(array $rows): array
    {
        foreach ($rows as &$row) {
            $row['source_url'] = 'https://www.themoviedb.org/movie/'.$row['id'];
            $this->sources[(string)$row['id']] = ['id'=>$row['id'],'title'=>$row['title'],'release_date'=>$row['release_date'],'url'=>$row['source_url']];
            if (isset($row['overview'])) { $row['overview'] = mb_substr($row['overview'],0,2400); }
        }
        return $rows;
    }
}
