<?php

declare(strict_types=1);

namespace App\Search;

interface EmbedderInterface
{
    public function model(): string;

    public function dimensions(): int;

    public function digest(): string;

    /** @param list<string> $texts
     * @return list<list<float>>
     */
    public function embed(array $texts, string $digest, bool $query = false): array;
}
