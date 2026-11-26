<?php

declare(strict_types=1);

namespace App\Search;

use Survos\SearchBundle\Attribute\AsSearch;
use Survos\SearchBundle\Search\AbstractSearch;
use Survos\SearchBundle\Search\HitTemplateSearchInterface;
use Survos\SearchBundle\Twig\Components\Facet\RangeInput;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsSearch(index: 'product_search', name: 'postgres_products', adapter: 'postgres_products')]
final class PostgresProductSearch extends AbstractSearch implements HitTemplateSearchInterface
{
    public function __construct(private readonly PostgresProductCatalog $catalog,
        #[Autowire('%env(float:PRODUCT_SEARCH_MIN_SIMILARITY)%')] private readonly float $minimumSimilarity) {}

    public function build(array $options = []): void
    {
        $manifest = $this->catalog->manifest() ?? throw new \RuntimeException('Run app:products:postgres:import first.');
        $this->addFacet('category', 'Category')->addFacet('brand', 'Brand')->addFacet('price', 'Price', RangeInput::class);
        $this->setAvailableHitsPerPage([12, 24, 48]);
        $this->addAvailableSort(null, 'Relevance')->addAvailableSort('price:asc', 'Price: low to high');
        $this->setAdapterParameters([
            'table' => 'product_search',
            'selectColumns' => ['d.id', "d.metadata->>'title' AS title", "d.metadata->>'description' AS description", "d.metadata->>'thumbnail' AS thumbnail", "d.metadata->>'category' AS category", "CAST(d.metadata->>'price' AS DOUBLE PRECISION) AS price"],
            'facetColumns' => ['category' => "d.metadata->>'category'", 'brand' => "d.metadata->>'brand'", 'price' => "CAST(d.metadata->>'price' AS DOUBLE PRECISION)"],
            'sortColumns' => ['price' => "CAST(d.metadata->>'price' AS DOUBLE PRECISION)"],
            'matchExpression' => "to_tsvector('english', d.search_text) @@ plainto_tsquery('english', :bm25Query)",
            'scoreExpression' => "ts_rank_cd(to_tsvector('english', d.search_text), plainto_tsquery('english', :bm25Query))",
            'vectorColumn' => 'd.embedding', 'vectorDimensions' => $manifest['dimensions'],
            'vectorProfile' => $manifest['digest'], 'minimumSimilarity' => $this->minimumSimilarity, 'queryEmbedder' => $this->catalog,
        ]);
    }

    public function getHitTemplate(): ?string { return 'search/product_card.html.twig'; }
}
