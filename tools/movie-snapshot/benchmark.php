<?php
declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use Survos\DuckDbBundle\ConnectionFactory;
use Survos\DuckDbBundle\S3Configuration;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\HttpClient;

if ($argc !== 2) { fwrite(STDERR, "Usage: php tools/movie-snapshot/benchmark.php /absolute/file.duckdb|s3://bucket/key\n"); exit(2); }
(new Dotenv())->bootEnv(dirname(__DIR__, 2).'/.env');
$env = static fn (string $key, string $default = ''): string => $_SERVER[$key] ?? $_ENV[$key] ?? $default;
$uri = $argv[1];
$s3 = str_starts_with($uri, 's3://') ? new S3Configuration(
    endpoint: $env('MOVIE_S3_ENDPOINT', 'http://127.0.0.1:8333'), region: 'us-east-1', urlStyle: 'path',
    accessKey: $env('MOVIE_S3_ACCESS_KEY'), secretKey: $env('MOVIE_S3_SECRET_KEY'),
) : null;
$client = HttpClient::create();
$host = rtrim($env('OLLAMA_HOST', 'http://127.0.0.1:11434'), '/');
$models = $client->request('GET', $host.'/api/tags', ['timeout'=>15])->toArray()['models'];
$digest = null;
foreach ($models as $model) { if (in_array($model['name'], ['nomic-embed-text', 'nomic-embed-text:latest'], true)) { $digest = $model['digest']; } }
$start = microtime(true);
$response = $client->request('POST', $host.'/api/embed', ['json'=>['model'=>'nomic-embed-text', 'input'=>'search_query: science fiction about memory and identity', 'truncate'=>false], 'timeout'=>120])->toArray();
$embeddingMs = (microtime(true)-$start)*1000;
$vector = $response['embeddings'][0];
if (count($vector) !== 768) { throw new RuntimeException('Expected 768 query dimensions.'); }
$start = microtime(true);
$db = (new ConnectionFactory())->open($uri, $s3);
$db->exec("SET threads=4; SET memory_limit='4GB'; SET auto_fallback_to_full_download=false");
$report = ['uri'=>$uri, 'query_model'=>'nomic-embed-text', 'query_model_digest'=>$digest, 'embedding_ms'=>round($embeddingMs,2), 'open_ms'=>round((microtime(true)-$start)*1000,2), 'note'=>'First full-vector query on a fresh connection, then warm repeats; OS/server caches are not cleared.', 'queries'=>[]];
$encoded = json_encode($vector, JSON_THROW_ON_ERROR);
$queries = [
    'semantic_all'=>['SELECT id, title, 1-array_cosine_distance(embedding, CAST(? AS FLOAT[768])) AS similarity FROM movies ORDER BY similarity DESC, id LIMIT 8', [$encoded]],
    'director'=>["SELECT id, title FROM movies WHERE contains(lower(director), 'kurosawa') AND release_date BETWEEN '1950-01-01' AND '1959-12-31' ORDER BY vote_count DESC NULLS LAST, id LIMIT 5", []],
    'semantic_filtered'=>['SELECT id, title, 1-array_cosine_distance(embedding, CAST(? AS FLOAT[768])) AS similarity FROM movies WHERE vote_count >= 100 ORDER BY similarity DESC, id LIMIT 8', [$encoded]],
];
foreach ($queries as $name=>[$sql,$parameters]) {
    for ($i=1; $i<=3; ++$i) {
        $start = microtime(true);
        $stmt = $db->prepare($sql);
        $stmt->execute($parameters);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $report['queries'][] = ['name'=>$name, 'run'=>$i, 'milliseconds'=>round((microtime(true)-$start)*1000,2), 'results'=>$rows];
    }
}
echo json_encode($report, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR).PHP_EOL;
