<?php

declare(strict_types=1);

namespace App\Search;

use Survos\DuckDbBundle\NamedSources;

/** Rebuildable projection including the existing publisher vectors; never embeds documents. */
final readonly class MovieSnapshotDocuments
{
    public function __construct(private NamedSources $sources) {}

    public function __invoke(): \Generator
    {
        yield from $this->fromId(0);
    }

    public function fromId(int $start): \Generator
    {
        $db = $this->sources->open('movie_snapshot');
        $db->exec("SET threads=4; SET memory_limit='4GB'");
        $max = (int) $db->query('SELECT max(id) FROM movies')->fetchColumn();
        // Bound native result memory while retaining zone-map pruning on the ID-sorted snapshot.
        for ($lower = max(0, intdiv($start, 10000)*10000); $lower <= $max; $lower += 10000) {
            $statement = $db->query('SELECT id, title, original_title, overview, tagline, director, movie_cast, genres, original_language, status, poster_path, release_date, year(try_cast(release_date AS DATE)) AS year, vote_count, vote_average, popularity, to_json(embedding) AS embedding FROM movies WHERE id >= '.$lower.' AND id < '.($lower+10000));
            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $row['embedding'] = is_array($row['embedding']) ? $row['embedding'] : json_decode($row['embedding'], true, flags: JSON_THROW_ON_ERROR);
                if (count($row['embedding']) !== 768 || !array_all($row['embedding'], static fn ($v): bool => is_numeric($v) && is_finite((float) $v)) || 0.0 === array_sum(array_map(static fn ($v): float => $v*$v, $row['embedding']))) {
                    throw new \RuntimeException('Invalid source vector for movie '.$row['id']);
                }
                yield self::project($row);
            }
        }
    }

    public static function project(array $row): array
    {
        $row['id'] = (int) $row['id'];
        $row['genres'] = array_values(array_unique(array_filter(array_map('trim', explode(',', $row['genres'] ?? '')), static fn (string $value): bool => '' !== $value)));
        foreach (['year', 'vote_count'] as $field) {
            $row[$field] = null === $row[$field] ? null : (int) $row[$field];
        }
        foreach (['vote_average', 'popularity'] as $field) {
            $row[$field] = null === $row[$field] ? null : (float) $row[$field];
        }
        $row['snapshot'] = 'hf-early-2025';
        return $row;
    }
}
