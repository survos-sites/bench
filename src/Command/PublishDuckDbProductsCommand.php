<?php

declare(strict_types=1);

namespace App\Command;

use App\Search\NomicEmbedder;
use App\Search\SnapshotRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand('app:duckdb:products:publish', 'Publish the original local Nomic snapshot to S3 without re-embedding')]
final class PublishDuckDbProductsCommand
{
    public function __construct(
        private readonly SnapshotRepositoryInterface $repository,
        #[Autowire('%kernel.project_dir%/var/data/duckdb-products')]
        private readonly string $directory,
    ) {
    }

    public function __invoke(SymfonyStyle $io): int
    {
        $manifest = json_decode(file_get_contents($this->directory.'/current.json'), true, flags: JSON_THROW_ON_ERROR);
        if (basename($manifest['file']) !== $manifest['file'] || NomicEmbedder::MODEL !== $manifest['model']) {
            throw new \RuntimeException('Expected the original local Nomic product snapshot.');
        }
        $manifest += ['dataset' => 'products', 'profile' => 'nomic', 'table' => 'products', 'dimensions' => NomicEmbedder::DIMENSIONS];
        $published = $this->repository->publish($this->directory.'/'.$manifest['file'], $manifest);
        $io->success(sprintf('Published and queried %d products remotely: %s', $published['count'], $published['uri']));

        return Command::SUCCESS;
    }
}
