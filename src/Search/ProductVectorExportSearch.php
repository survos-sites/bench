<?php

declare(strict_types=1);

namespace App\Search;

use App\Entity\Product;
use Survos\SearchBundle\Attribute\AsSearch;
use Survos\SearchBundle\Search\AbstractSearch;

/** ES projection uses the persisted vectors; its indexing path has no embedding provider. */
#[AsSearch(index: Product::class, name: 'product_vectors', adapter: 'es')]
final class ProductVectorExportSearch extends AbstractSearch
{
    public function __construct(private readonly PostgresProductCatalog $catalog) {}

    public function build(array $options = []): void
    {
        $manifest = $this->catalog->manifest() ?? throw new \RuntimeException('Run app:products:postgres:import first.');
        $mapping = [];
        foreach (['id', 'category', 'brand', 'embeddingFingerprint', 'contentHash', 'embeddingProfile', 'embeddingModel'] as $field) {
            $mapping[$field] = ['type' => 'keyword'];
        }
        $mapping += [
            'title' => ['type' => 'text'], 'description' => ['type' => 'text'],
            'thumbnail' => ['type' => 'keyword', 'index' => false], 'price' => ['type' => 'double'],
            'embeddingDimensions' => ['type' => 'integer'], 'textVersion' => ['type' => 'integer'],
            'embedding' => ['type' => 'dense_vector', 'dims' => $manifest['dimensions'], 'index' => false],
        ];
        $this->setAdapterParameters([
            // Different snapshots/profiles cannot leave stale products or dimensions in an index.
            'index' => 'product_vectors_'.substr($manifest['file'], 0, 16),
            'mappings' => $mapping, 'vectorDimensions' => $manifest['dimensions'],
            'documentProvider' => $this->catalog->elasticDocuments(...),
            'searchFields' => ['title', 'description'], 'sourceFields' => array_keys($mapping),
        ]);
    }
}
