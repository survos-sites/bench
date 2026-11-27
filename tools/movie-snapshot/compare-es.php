<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use Survos\DuckDbBundle\ConnectionFactory;
use Symfony\Component\HttpClient\HttpClient;

if ($argc !== 4) {
    fwrite(STDERR, "Usage: php tools/movie-snapshot/compare-es.php snapshot.duckdb http://localhost:9200 index-alias\n");
    exit(2);
}
$db = (new ConnectionFactory())->open($argv[1]);
$db->exec("SET threads=4; SET memory_limit='4GB'");
$http = HttpClient::create();
$base = rtrim($argv[2], '/').'/'.rawurlencode($argv[3]);
$esCount = $http->request('GET', $base.'/_count')->toArray()['count'];
$duckCount = (int) $db->query('SELECT count(*) FROM movies')->fetchColumn();
if ($esCount !== $duckCount) { throw new RuntimeException('Snapshot counts differ.'); }
$report = ['measured_at'=>gmdate(DATE_ATOM), 'rows'=>$duckCount, 'index'=>$argv[3], 'note'=>'First measured and warm repeats, caches not cleared. ES HTTP includes transport/JSON; took is engine time. DuckDB returns rows and exact total in one SQL statement. No LLM or embedding calls.', 'runs'=>[]];
$cases = [
    'language_genre_year_votes'=>[
        "original_language='en' AND list_contains(list_transform(string_split(coalesce(genres,''), ','), lambda x: trim(x)), 'Science Fiction') AND year(try_cast(release_date AS DATE)) BETWEEN 1980 AND 2020 AND vote_count >= 100",
        ['bool'=>['filter'=>[['term'=>['original_language'=>'en']], ['term'=>['genres'=>'Science Fiction']], ['range'=>['year'=>['gte'=>1980,'lte'=>2020]]], ['range'=>['vote_count'=>['gte'=>100]]]]]],
    ],
    'japanese_1950s'=>[
        "original_language='ja' AND year(try_cast(release_date AS DATE)) BETWEEN 1950 AND 1959",
        ['bool'=>['filter'=>[['term'=>['original_language'=>'ja']], ['range'=>['year'=>['gte'=>1950,'lte'=>1959]]]]]],
    ],
];
foreach ($cases as $name=>[$where,$query]) {
    for ($run=1; $run<=3; ++$run) {
        $start = hrtime(true);
        $rows = $db->query('SELECT id, count(*) OVER() AS total FROM movies WHERE '.$where.' ORDER BY vote_count DESC NULLS LAST, id LIMIT 24')->fetchAll(PDO::FETCH_ASSOC);
        $duckMs = (hrtime(true)-$start)/1e6;
        $start = hrtime(true);
        $es = $http->request('POST', $base.'/_search', ['json'=>['query'=>$query,'size'=>24,'track_total_hits'=>true,'_source'=>['id'], 'sort'=>[['vote_count'=>['order'=>'desc','missing'=>'_last']],['id'=>'asc']]]])->toArray();
        $esMs = (hrtime(true)-$start)/1e6;
        $ids = array_column($rows,'id');
        $esIds = array_map(static fn(array $hit): int => $hit['_source']['id'], $es['hits']['hits']);
        $total = $rows[0]['total'] ?? 0;
        if ($ids !== $esIds || $total !== $es['hits']['total']['value']) { throw new RuntimeException('Filter results differ: '.$name); }
        $report['runs'][] = ['case'=>$name,'run'=>$run,'total'=>$total,'ids'=>$ids,'duckdb_ms'=>round($duckMs,2),'es_http_ms'=>round($esMs,2),'es_took_ms'=>$es['took']];
    }
}
$report['stats'] = $http->request('GET', $base.'/_stats/docs,store')->toArray()['_all']['primaries'];
echo json_encode($report, JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR)."\n";
