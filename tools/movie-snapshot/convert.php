<?php
declare(strict_types=1);

// Standalone offline conversion. No Symfony boot, source network access or model calls.
if ($argc !== 3) { fwrite(STDERR, "Usage: php tools/movie-snapshot/convert.php source.parquet output.duckdb\n"); exit(2); }
[$script, $input, $output] = $argv;
if (!is_file($input) || file_exists($output) || file_exists($output.'.building')) { throw new RuntimeException('Require an existing input and unused output paths.'); }
$start = microtime(true);
$staging = $output.'.building';
$db = new PDO('duckdb:'.$staging);
// This specific source has a 6.5 GiB embedding column chunk in one row group.
$db->exec("SET memory_limit='16GB'; SET threads=2; SET preserve_insertion_order=false");
$db->exec('SET temp_directory='.$db->quote($output.'.spill'));
$db->exec('CREATE TABLE movies AS SELECT * EXCLUDE (embedding), TRY_CAST(embedding AS FLOAT[768]) AS embedding FROM read_parquet('.$db->quote(realpath($input)).')');
$conversionSeconds = microtime(true)-$start;
$validation = $db->query("SELECT count(*) AS rows, count(DISTINCT id) AS distinct_ids, count(*) FILTER (WHERE id IS NULL) AS missing_ids, count(*) FILTER (WHERE embedding IS NULL) AS invalid_embeddings, count(*) FILTER (WHERE embedding IS NOT NULL AND (list_count(list_filter(embedding, lambda x: x IS NULL OR NOT isfinite(x))) > 0 OR array_inner_product(embedding, embedding) = 0)) AS unusable_vectors FROM movies")->fetch(PDO::FETCH_ASSOC);
if ($validation['rows'] === 0 || $validation['rows'] !== $validation['distinct_ids'] || $validation['missing_ids'] || $validation['invalid_embeddings'] || $validation['unusable_vectors']) { throw new RuntimeException('Snapshot validation failed: '.json_encode($validation)); }
$version = $db->query('SELECT version()')->fetchColumn();
$db->exec('CREATE UNIQUE INDEX movies_id ON movies(id)');
$db->exec('CHECKPOINT');
$db = null;
if (file_exists($staging.'.wal')) { throw new RuntimeException('Unmerged WAL; refusing publication.'); }
rename($staging, $output);
$manifest = [
    'source'=>'https://huggingface.co/datasets/remsky/Embeddings__Ultimate_1Million_Movies_Dataset',
    'upstream'=>'https://www.kaggle.com/datasets/alanvourch/tmdb-movies-daily-updates',
    'snapshot'=>'early 2025 (publisher description; exact capture time unavailable)',
    'source_sha256'=>hash_file('sha256', $input), 'source_bytes'=>filesize($input),
    'duckdb_version'=>$version, 'schema_version'=>1, 'primary_key'=>'id (TMDB)',
    'embedding'=>['model'=>'nomic-embed-text:latest', 'source_model_digest'=>null, 'type'=>'FLOAT[768]', 'dimensions'=>768, 'reembedded'=>false, 'input_caveat'=>'Publisher reports genres included for most rows but omitted in initial 10–15%; exact prefix unspecified.'],
    'absent_fields'=>['keywords','US certifications / MPAA ratings'],
    'validation'=>$validation, 'conversion_seconds'=>round($conversionSeconds,2),
    'total_seconds'=>round(microtime(true)-$start,2), 'built_at'=>gmdate(DATE_ATOM),
    'bytes'=>filesize($output), 'sha256'=>hash_file('sha256',$output),
];
file_put_contents($output.'.manifest.json', json_encode($manifest, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR)."\n");
echo json_encode($manifest, JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR).PHP_EOL;
