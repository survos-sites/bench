<?php

declare(strict_types=1);

namespace App\Search;

use Survos\SearchBundle\Attribute\AsSearch;
use Survos\SearchBundle\Search\AbstractSearch;
use Survos\SearchBundle\Search\HitTemplateSearchInterface;
use Survos\SearchBundle\Twig\Components\Facet\RangeInput;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsSearch(index: 'products', name: 'local_products', adapter: 'local_products')]
final class ProductCatalogSearch extends AbstractSearch implements HitTemplateSearchInterface
{
    public function __construct(private readonly LocalProductCatalog $catalog,
        #[Autowire('%env(float:PRODUCT_SEARCH_MIN_SIMILARITY)%')] private readonly float $minimumSimilarity)
    {
    }

    public function build(array $options = []): void
    {
        $this->addFacet('category', 'Category')->addFacet('brand', 'Brand');
        $this->addFacet('price', 'Price', RangeInput::class);
        $this->setAvailableHitsPerPage([12, 24, 48]);
        $this->addAvailableSort(null, 'Relevance');
        $this->addAvailableSort('price_asc', 'Price: low to high');
        $p = [
            'table' => 'products', 'selectColumns' => ['d.id', "d.metadata->>'title' AS title", "d.metadata->>'description' AS description", "d.metadata->>'thumbnail' AS thumbnail", "d.metadata->>'category' AS category", "TRY_CAST(d.metadata->>'price' AS DOUBLE) AS price"],
            'facetColumns' => ['category' => "d.metadata->>'category'", 'brand' => "d.metadata->>'brand'", 'price' => "TRY_CAST(d.metadata->>'price' AS DOUBLE)"],
            'rangeFacets' => ['price'], 'ftsFunction' => 'fts_main_products.match_bm25',
            'sortColumns' => ['price_asc' => "TRY_CAST(d.metadata->>'price' AS DOUBLE) ASC"],
        ];
        if ($this->catalog->hasVectors()) {
            $p += ['vectorColumn' => 'd.embedding', 'vectorDimensions' => $this->catalog->manifest()['dimensions'],
                'minimumSimilarity' => $this->minimumSimilarity, 'queryEmbedder' => $this->catalog];
        }
        $this->setAdapterParameters($p);
    }

    public function getHitTemplate(): ?string
    {
        return 'search/product_card.html.twig';
    }
}
