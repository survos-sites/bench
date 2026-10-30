<?php

declare(strict_types=1);

namespace App\Command;

use App\Search\DuckDbSearch;
use App\Search\EmbeddingProfiles;
use App\Search\LocalProductCatalog;
use App\Search\SearchDatasets;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:duckdb:products:local', 'Build a local products database with FTS and cached embeddings')]
final class BuildLocalProductsCommand
{
    public function __construct(private SearchDatasets $datasets, private EmbeddingProfiles $profiles, private LocalProductCatalog $catalog)
    {
    }

    public function __invoke(SymfonyStyle $io, #[Option('Embedding profile: openai or nomic')] string $embedder = 'openai'): int
    {
        $result = (new DuckDbSearch($this->datasets, $this->profiles, $this->catalog))->index('products', $embedder);
        $io->success(sprintf('%d products indexed locally with FTS and %s vectors.', $result['count'], $embedder));

        return 0;
    }
}
