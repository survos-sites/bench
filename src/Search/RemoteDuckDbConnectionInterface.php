<?php

declare(strict_types=1);

namespace App\Search;

interface RemoteDuckDbConnectionInterface
{
    public function open(string $url, #[\SensitiveParameter] array $s3 = []): \PDO;
}
