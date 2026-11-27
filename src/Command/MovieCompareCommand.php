<?php

declare(strict_types=1);

namespace App\Command;

use App\Search\MovieElasticCatalog;
use App\Search\NomicEmbedder;
use Survos\DuckDbBundle\NamedSources;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:movies:compare', 'Compare existing ES vectors with exact DuckDB cosine; only queries are embedded')]
final readonly class MovieCompareCommand
{
    public function __construct(private MovieElasticCatalog $catalog, private NamedSources $sources, private NomicEmbedder $embedder) {}

    public function __invoke(SymfonyStyle $io, #[Option('ANN candidate count')] int $candidates = 1000): int
    {
        $db = $this->sources->open('movie_snapshot');
        $db->exec("SET threads=4; SET memory_limit='4GB'");
        $report = ['measured_at'=>gmdate(DATE_ATOM), 'query_model_digest'=>$this->embedder->digest(), 'document_embeddings'=>'existing HF 2025 FLOAT[768], no re-embedding', 'candidates'=>$candidates, 'k'=>8, 'cache_note'=>'First measured and two warm repeats; OS caches not cleared; query embedding excluded from retrieval timings.', 'runs'=>[]];
        foreach (['science fiction about memory and identity','a lonely robot finds friendship','Japanese samurai defending a village','a musician struggles with ambition and fame','underwater exploration and dangerous sea creatures'] as $query) {
            $start = hrtime(true);
            $vector = $this->embedder->embed([$query], $report['query_model_digest'], query:true)[0];
            $embedMs = (hrtime(true)-$start)/1e6;
            foreach ([0,100] as $votes) {
                for ($run=1; $run<=3; ++$run) {
                    $start = hrtime(true);
                    $statement = $db->prepare('SELECT id,title,1-array_cosine_distance(embedding,CAST(? AS FLOAT[768])) AS similarity FROM movies WHERE vote_count >= ? ORDER BY similarity DESC,id LIMIT 8');
                    // With no vote constraint include null vote_count, as the ES all-record search does.
                    if (0 === $votes) { $statement = $db->prepare('SELECT id,title,1-array_cosine_distance(embedding,CAST(? AS FLOAT[768])) AS similarity FROM movies ORDER BY similarity DESC,id LIMIT 8'); $statement->execute([json_encode($vector,JSON_THROW_ON_ERROR)]); }
                    else { $statement->execute([json_encode($vector,JSON_THROW_ON_ERROR),$votes]); }
                    $exact = $statement->fetchAll(\PDO::FETCH_ASSOC);
                    $duckMs = (hrtime(true)-$start)/1e6;
                    $start = hrtime(true);
                    $es = $this->catalog->retrieve($vector, $votes ? [['range'=>['vote_count'=>['gte'=>$votes]]]] : [], 8, $candidates);
                    $esMs = (hrtime(true)-$start)/1e6;
                    $actual = array_map(static fn(array $row): array => array_intersect_key($row,array_flip(['id','title','similarity'])), $es['movies']);
                    $report['runs'][] = ['query'=>$query,'min_votes'=>$votes,'run'=>$run,'query_embedding_ms'=>round($embedMs,2),'duckdb_ms'=>round($duckMs,2),'es_http_ms'=>round($esMs,2),'es_engine_ms'=>$es['engine_ms'],'recall_at_8'=>count(array_intersect(array_column($exact,'id'),array_column($actual,'id')))/8,'duckdb'=>$exact,'elasticsearch'=>$actual];
                }
            }
        }
        $io->writeln(json_encode($report,JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR));
        return 0;
    }
}
