<?php

declare(strict_types=1);

namespace App\Command;

use App\Search\DuckDbSearch;
use App\Search\EmbeddingProfiles;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:duckdb:index', 'Build and publish a semantic search snapshot to S3', aliases: ['app:duckdb:products:index'])]
final class IndexDuckDbCommand
{
    public function __construct(private readonly DuckDbSearch $search, private readonly EmbeddingProfiles $profiles)
    {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Option(description: 'products or movies')] string $dataset = 'products',
        #[Option(name: 'embedder', description: 'openai or nomic (defaults to DUCKDB_EMBEDDING_PROFILE)')] ?string $profile = null,
        #[Option(description: 'Refresh the cached product download')] bool $refresh = false,
    ): int {
        $profile ??= $this->profiles->default();
        $result = $this->search->index($dataset, $profile, $refresh, static function (int $done, int $total) use ($io): void {
            $io->writeln(sprintf('Indexed %d / %d records', $done, $total));
        });
        $io->success(sprintf('%d %s published with %s in %.2fs: %s', $result['count'], $dataset, $profile, $result['seconds'], $result['manifest']['uri']));

        return Command::SUCCESS;
    }
}
