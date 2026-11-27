<?php

declare(strict_types=1);

namespace App\Command;

use App\Search\MovieElasticClient;
use App\Search\MovieSnapshotDocuments;
use Survos\DuckDbBundle\NamedSources;
use Survos\ElasticBundle\Service\ElasticIndexService;
use Survos\SearchBundle\Adapter\Elasticsearch\ElasticsearchClient;
use Survos\SearchBundle\Service\ElasticIndexNameResolver;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:movies:resume-index', 'Resume an interrupted unpublished vector generation and validate before alias swap')]
final readonly class MovieResumeIndexCommand
{
    public function __construct(private MovieElasticClient $client, private MovieSnapshotDocuments $documents, private NamedSources $sources, private ElasticIndexService $indexer, private ElasticIndexNameResolver $names) {}

    public function __invoke(#[Argument('Existing unpublished generation')] string $generation, SymfonyStyle $io): int
    {
        $alias = $this->client->index();
        $client = new ElasticsearchClient($this->client->native());
        if (!$this->names->isGenerationOf($generation,$alias) || !$client->indexExists($generation) || $client->getAliases($generation)) {
            throw new \RuntimeException('Require an existing unpublished movie generation.');
        }
        $mapping = $client->getMapping($generation);
        if (768 !== ($mapping['properties']['embedding']['dims'] ?? null)) { throw new \RuntimeException('Require 768-dimensional vector mapping.'); }
        $client->refresh($generation);
        $result = $this->client->native()->search(['index'=>$generation,'body'=>['size'=>0,'aggs'=>['max_id'=>['max'=>['field'=>'id']]]]])->asArray();
        // Re-send the last entire 10,000-ID window: an interrupted bulk may have partially succeeded.
        $start = max(0, (int)($result['aggregations']['max_id']['value'] ?? 0)-10000);
        $io->writeln('Resuming at ID window '.$start);
        $items = function () use ($start): \Generator {
            foreach ($this->documents->fromId($start) as $document) { yield ['id'=>(string)$document['id'],'document'=>$document]; }
        };
        $count = $this->indexer->bulkIndex($client,$generation,$items(),500);
        $client->refresh($generation);
        $db = $this->sources->open('movie_snapshot');
        $expected = (int)$db->query('SELECT count(*) FROM movies')->fetchColumn();
        $actual = $this->client->native()->count(['index'=>$generation,'body'=>['query'=>['exists'=>['field'=>'embedding']]]])->asArray()['count'];
        if ($actual !== $expected) { throw new \RuntimeException('Vector count differs; alias unchanged.'); }
        $client->putSettings($generation,['index'=>['refresh_interval'=>null]]);
        $actions = [['add'=>['index'=>$generation,'alias'=>$alias]]];
        foreach ($client->indicesForAlias($alias) as $old) { $actions[] = ['remove'=>['index'=>$old,'alias'=>$alias]]; }
        $client->updateAliases($actions);
        $io->success(sprintf('%d documents resent; %d vectors verified; alias switched. Old generation retained.', $count,$actual));
        return 0;
    }
}
