<?php

declare(strict_types=1);

namespace App\Search;

interface SnapshotRepositoryInterface
{
    public function manifest(string $dataset, string $profile): ?array;

    public function publish(string $path, array $manifest): array;

    public function open(array $manifest): \PDO;
}
