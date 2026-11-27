<?php

declare(strict_types=1);

namespace App\Search;

use Survos\SearchBundle\Attribute\AsSearch;
use Survos\SearchBundle\Search\AbstractSearch;
use Survos\SearchBundle\Twig\Components\Facet\RangeInput;

#[AsSearch(index: MovieSnapshotDocuments::class, name: 'movie_snapshot_es', adapter: 'es')]
final class MovieSnapshotSearch extends AbstractSearch
{
    public function __construct(private readonly MovieSnapshotDocuments $documents) {}

    public function build(array $options = []): void
    {
        $this->addFacet('genres', 'Genre')->addFacet('original_language', 'Original language')->addFacet('status', 'Status');
        $this->addFacet('year', 'Release year', RangeInput::class);
        $this->addFacet('vote_count', 'TMDB votes', RangeInput::class);
        $this->setAvailableHitsPerPage([24, 48]);
        $this->addAvailableSort(null, 'Relevance');
        $this->addAvailableSort('vote_count:desc', 'Most votes');
        $this->addAvailableSort('year:desc', 'Newest release');
        $this->addAvailableSort('year:asc', 'Oldest release');
        $mappings = [];
        foreach (['title', 'original_title', 'overview', 'tagline', 'director', 'movie_cast'] as $field) { $mappings[$field] = ['type'=>'text']; }
        foreach (['genres', 'original_language', 'status', 'snapshot', 'poster_path', 'release_date'] as $field) { $mappings[$field] = ['type'=>'keyword']; }
        foreach (['id', 'year', 'vote_count'] as $field) { $mappings[$field] = ['type'=>'long']; }
        foreach (['vote_average', 'popularity'] as $field) { $mappings[$field] = ['type'=>'float']; }
        foreach (['title', 'director'] as $field) { $mappings[$field]['fields']['literal'] = ['type'=>'wildcard']; }
        // Float HNSW avoids quantization changes in this first baseline comparison.
        $mappings['embedding'] = ['type'=>'dense_vector', 'dims'=>768, 'similarity'=>'cosine', 'index'=>true, 'index_options'=>['type'=>'hnsw']];
        $this->setAdapterParameters([
            // The registry requires a class; the explicit index keeps the ES alias stable.
            'index'=>'movie_snapshot_2025', 'documentProvider'=>$this->documents,
            'mappings'=>$mappings, 'sourceFields'=>array_values(array_diff(array_keys($mappings), ['embedding'])),
            'documentMapper'=>static fn (array $document): array => $document,
            'searchFields'=>['title^5', 'original_title^3', 'director^3', 'movie_cast', 'tagline^2', 'overview'],
            'facetFields'=>['genres'=>'genres', 'original_language'=>'original_language', 'status'=>'status', 'year'=>'year', 'vote_count'=>'vote_count'],
            'sortFields'=>['id'=>'id', 'vote_count'=>'vote_count', 'year'=>'year'],
        ]);
    }
}
