<?php

declare(strict_types=1);

namespace App\Command;

use App\Search\LocalProductCatalog;
use App\Search\PostgresProductCatalog;
use Survos\SearchBundle\Search\Filter\RangeFilter;
use Survos\SearchBundle\Search\Filter\TermFilter;
use Survos\SearchBundle\Search\Query;
use Survos\SearchBundle\Search\Searcher;
use Survos\SearchBundle\Search\SearchProvider;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:products:compare', 'Compare DuckDB and PostgreSQL vector results, facets and every result page')]
final readonly class CompareProductVectorsCommand
{
    public function __construct(private SearchProvider $provider, private Searcher $searcher, private LocalProductCatalog $duckdb, private PostgresProductCatalog $postgres) {}

    public function __invoke(SymfonyStyle $io, #[Argument] string $query = 'tech gifts', #[Option] ?string $category = null, #[Option] ?float $maxPrice = null, #[Option] float $cutoff = 0.3): int
    {
        if (($this->duckdb->manifest()['file'] ?? null) !== ($this->postgres->manifest()['file'] ?? null)) {
            throw new \RuntimeException('Import the current DuckDB snapshot into PostgreSQL before comparing.');
        }
        $results = [];
        foreach (['local_products', 'postgres_products'] as $name) {
            $search = $this->provider->getSearch($name)->create();
            $q = (new Query())->setSearchMode('semantic')->setQueryString($query)->setMinimumSimilarity($cutoff)->setActiveHitsPerPage(12);
            if (null !== $category) { $q->addActiveFilter(new TermFilter('category', [$category])); }
            if (null !== $maxPrice) { $q->addActiveFilter(new RangeFilter('price', null, $maxPrice)); }
            $first = $this->searcher->search($q, $search);
            $ids = [];
            for ($page = 1; $page <= max(1, (int) ceil($first->getTotalResults() / 12)); ++$page) {
                $result = 1 === $page ? $first : $this->searcher->search($q->setCurrentPage($page), $search);
                if ($first->getTotalResults() !== $result->getTotalResults()) { throw new \RuntimeException('Result total changed between pages.'); }
                foreach ($result->getHits() as $hit) { $ids[] = $hit->getData()['id']; }
            }
            if (count(array_unique($ids)) !== $first->getTotalResults()) { throw new \RuntimeException('Pagination has gaps or duplicate records.'); }
            $facets = [];
            foreach ($first->getFacetDistributions() as $facet) {
                if ('price' === $facet->getProperty()) { continue; }
                $values = $facet->getValues(); ksort($values); $facets[$facet->getProperty()] = $values;
            }
            $stats = [];
            foreach ($first->getFacetStats() as $stat) { $stats[$stat->getProperty()] = [(float) $stat->getMin(), (float) $stat->getMax()]; }
            $results[$name] = ['ids' => $ids, 'facets' => $facets, 'stats' => $stats];
        }
        if ($results['local_products'] !== $results['postgres_products']) {
            $io->writeln(json_encode($results, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
            throw new \RuntimeException('The engines differ in result order, facet counts or range statistics.');
        }
        $io->success(sprintf('Both engines agree: %d matches, identical ordered IDs across all pages, facet counts and range statistics.', count($results['local_products']['ids'])));

        return 0;
    }
}
