<?php

declare(strict_types=1);

namespace App\Command;

use App\Search\PostgresProductCatalog;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:products:postgres:import', 'Copy the local product snapshot and existing vectors into PostgreSQL (no embedding calls)')]
final readonly class ImportPostgresProductsCommand
{
    public function __construct(private PostgresProductCatalog $catalog) {}

    public function __invoke(SymfonyStyle $io): int
    {
        $count = $this->catalog->import();
        $manifest = $this->catalog->manifest();
        $io->success(sprintf('Copied %d products and their existing %d-dimensional %s vectors into PostgreSQL.', $count, $manifest['dimensions'], $manifest['model']));

        return 0;
    }
}
