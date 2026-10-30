<?php

declare(strict_types=1);

namespace App\Command;

use App\Search\DuckDbProductSearch;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:duckdb:products:index', 'Build a cached DummyJSON/Nomic product search snapshot')]
final class IndexDuckDbProductsCommand
{
    public function __construct(private readonly DuckDbProductSearch $search)
    {
    }

    public function __invoke(SymfonyStyle $io, #[Option(description: 'Refresh the cached product download')] bool $refresh = false): int
    {
        $result = $this->search->index($refresh, static function (int $done, int $total) use ($io): void {
            $io->writeln(sprintf('Indexed %d / %d products', $done, $total));
        });
        $io->success(sprintf('%d products ready in %.2fs. Fetch cache: %s.', $result['count'], $result['seconds'], $result['fetchCached'] ? 'hit' : 'miss'));
        return Command::SUCCESS;
    }
}
