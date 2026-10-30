<?php

declare(strict_types=1);

namespace App\Command;

use App\Search\DuckDbSearch;
use App\Search\EmbeddingProfiles;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:duckdb:query', 'Query an S3 snapshot without downloading a local database')]
final class QueryDuckDbCommand
{
    public function __construct(private readonly DuckDbSearch $search, private readonly EmbeddingProfiles $profiles)
    {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument] string $query,
        #[Option] string $dataset = 'products',
        #[Option(name: 'embedder')] ?string $profile = null,
    ): int {
        $profile ??= $this->profiles->default();
        $snapshot = $this->search->snapshot($dataset, $profile) ?? throw new \RuntimeException('Snapshot not published.');
        $result = $this->search->search($dataset, $profile, $query, snapshot: $snapshot);
        $io->table(['Title', 'Cosine distance'], array_map(static fn (array $hit): array => [$hit['record']['title'], sprintf('%.4f', $hit['distance'])], $result['results']));
        $io->writeln(sprintf('%s · %d dimensions · %.1f MiB · remote %.0f ms · total %.0f ms', $snapshot['uri'], $snapshot['dimensions'], $snapshot['bytes'] / 1048576, $result['searchMs'], $result['totalMs']));

        return Command::SUCCESS;
    }
}
