<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:duckdb:check', 'Check native DuckDB and httpfs availability without installing anything')]
final class CheckDuckDbCommand
{
    public function __invoke(SymfonyStyle $io): int
    {
        if (!in_array('duckdb', \PDO::getAvailableDrivers(), true)) {
            $io->error('pdo_duckdb is missing. Build it into the deployed PHP runtime before enabling this demo.');

            return Command::FAILURE;
        }
        $pdo = new \PDO('duckdb::memory:');
        $version = $pdo->query('SELECT version()')->fetchColumn();
        try {
            $pdo->exec('LOAD httpfs');
        } catch (\Throwable) {
            $io->error('DuckDB '.$version.' is available, but its matching httpfs extension is missing.');

            return Command::FAILURE;
        }
        if (version_compare(ltrim($version, 'v'), '2.0.0', '<')) {
            $io->error('The current S3 snapshots require DuckDB 2.0 or newer; this runtime has '.$version.'.');

            return Command::FAILURE;
        }
        $io->success('PHP '.PHP_VERSION.' · PDO DuckDB '.$version.' · httpfs loaded. S3 credentials/connectivity are checked by app:duckdb:query.');

        return Command::SUCCESS;
    }
}
