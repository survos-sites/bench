<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\Attributes\Group;
use Survos\ElasticBundle\EventListener\ElasticSpoolDoctrineListener;
use Survos\ElasticBundle\Service\ElasticIndexService;
use Survos\ElasticBundle\Spool\ElasticSpooler;
use Survos\SearchBundle\Registry\UxSearchRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[Group('elastic-live')]
final class ElasticLifecycleTest extends KernelTestCase
{
    public function testPopulationAndDoctrineReconciliation(): void
    {
        if (getenv('ELASTIC_LIVE') !== '1') {
            self::markTestSkipped('Set ELASTIC_LIVE=1 with a local Elasticsearch node.');
        }
        $prefix = 'searchbench_test_'.bin2hex(random_bytes(6)).'_';
        $_ENV['SEARCH_INDEX_PREFIX'] = $_SERVER['SEARCH_INDEX_PREFIX'] = $prefix;
        $_ENV['DATABASE_URL'] = $_SERVER['DATABASE_URL'] = 'sqlite:///:memory:';
        self::bootKernel();
        $container = self::getContainer();
        $em = $container->get(EntityManagerInterface::class);
        (new SchemaTool($em))->createSchema([$em->getClassMetadata(Movie::class)]);
        $service = $container->get(ElasticIndexService::class);
        $directory = sys_get_temp_dir().'/'.$prefix;
        $spool = new ElasticSpooler($directory);
        $listener = new ElasticSpoolDoctrineListener($spool, $container->get(UxSearchRegistry::class), async: false, indexService: $service);
        $em->getEventManager()->addEventListener([Events::postPersist, Events::postUpdate, Events::preRemove, Events::postFlush, Events::onClear], $listener);
        [, $client] = $service->forEntityClass(Movie::class);
        $alias = $prefix.'movie';
        $io = new SymfonyStyle(new ArrayInput([]), new BufferedOutput());
        $drain = static fn () => $spool->drain(Movie::class, static fn (array $ids) => $service->indexIds(Movie::class, $ids));
        $title = static function () use ($client, $alias): ?string {
            // Incremental writes become searchable on refresh, not immediately after bulk.
            $client->refresh($alias);
            return $client->search($alias, ['query' => ['ids' => ['values' => ['900001']]]])['hits']['hits'][0]['_source']['title'] ?? null;
        };
        try {
            $movie = new Movie();
            $movie->id = 900001;
            $movie->title = 'Original test movie';
            $em->persist($movie);
            $em->flush();
            self::assertSame(['900001'], $spool->pendingIds(Movie::class));
            self::assertFalse($client->indexExists($alias));
            self::assertSame(0, $service->populateCommand($io, 'app_movie'));
            self::assertSame('Original test movie', $title());
            $drain();

            // Populate detaches its streamed entities; reload before changing state.
            $movie = $em->find(Movie::class, 900001);
            $movie->title = 'Updated test movie';
            $em->flush();
            $drain();
            self::assertSame('Updated test movie', $title());

            $em->remove($movie);
            $em->flush();
            $drain();
            self::assertNull($title());

            // A delayed deletion must reconcile the new row with the same identifier.
            $replacement = new Movie();
            $replacement->id = 900001;
            $replacement->title = 'Replacement movie';
            $em->persist($replacement);
            $em->flush();
            $service->indexIds(Movie::class, ['900001']);
            self::assertSame('Replacement movie', $title());

            // A rolled-back outer transaction is reconciled against committed DB state.
            $em->getConnection()->beginTransaction();
            $replacement->title = 'Rolled back';
            $em->flush();
            $em->getConnection()->rollBack();
            $em->clear();
            $drain();
            self::assertSame('Replacement movie', $title());
            self::assertSame([], $spool->pendingIds(Movie::class));

            foreach ([[900002, 'Batman & <script>alert(1)</script>', ['Action'], 1989], [900003, 'Batman Returns', ['Drama'], 1992], [900004, 'Other movie', ['Comedy'], 2020]] as [$id, $name, $genres, $year]) {
                $record = new Movie();
                $record->id = $id;
                $record->title = $name;
                $record->genres = $genres;
                $record->year = $year;
                $em->persist($record);
            }
            $em->flush();
            $drain();
            $client->refresh($alias);
            $gateway = $container->get(\Survos\SearchBundle\Http\InstantSearchGateway::class);
            $response = $gateway->search([
                ['indexName' => 'app_movie::year:desc', 'params' => ['query' => 'batmn', 'facetFilters' => [['genres:Action', 'genres:Drama']], 'numericFilters' => ['year>=1980', 'year<=2000']]],
                ['indexName' => 'app_movie', 'params' => ['query' => 'batmn', 'hitsPerPage' => 0]],
            ]);
            self::assertSame(2, $response['results'][0]['nbHits']);
            self::assertSame('900003', $response['results'][0]['hits'][0]['objectID']);
            self::assertSame([], $response['results'][1]['hits']);
            self::assertSame(2, $response['results'][1]['nbHits']);
            self::assertStringContainsString('&lt;script&gt;', $response['results'][0]['hits'][1]['_highlightResult']['title']['value']);
            $filtered = $gateway->search([['indexName' => 'app_movie', 'params' => ['query' => 'batmn', 'facetFilters' => [['genres:Action']]]]])['results'][0];
            self::assertSame(1, $filtered['nbHits']);
            self::assertSame(1, ((array) $filtered['facets']->genres)['Drama']);
            $prefixResult = $gateway->search([['indexName' => 'app_movie', 'params' => ['query' => 'batm']]])['results'][0];
            self::assertSame(2, $prefixResult['nbHits']);

        } finally {
            foreach ($client->listIndices($prefix.'*') as $index) { $client->deleteIndex($index['index']); }
            (new Filesystem())->remove($directory);
            unset($_ENV['SEARCH_INDEX_PREFIX'], $_SERVER['SEARCH_INDEX_PREFIX']);
        }
    }
}
