<?php

declare(strict_types=1);

namespace App\Search;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/** Explicit entity-to-dataset mapping: never guess a DuckDB index from an entity name. */
final readonly class EntityDuckDbLinks
{
    private const array DATASETS = [
        'app_movie' => ['dataset' => 'movies', 'route' => 'demo_movie_duckdb'],
    ];

    public function __construct(private SnapshotRepositoryInterface $snapshots, private CacheInterface $cache,
        private EmbeddingProfiles $profiles, private LoggerInterface $logger) {}

    public function forEntity(string $code): ?array
    {
        if (!isset(self::DATASETS[$code])) {
            return null;
        }
        $target = self::DATASETS[$code];
        $profile = $this->cache->get('entity_duckdb_link_'.hash('sha256', $code.$this->profiles->default()), function (ItemInterface $item) use ($target): ?string {
            $item->expiresAfter(60);
            try {
                foreach (array_unique([$this->profiles->default(), 'openai', 'nomic']) as $candidate) {
                    if (null !== $this->snapshots->manifest($target['dataset'], $candidate)) {
                        return $candidate;
                    }
                }
            } catch (\RuntimeException) {
                $this->logger->warning('DuckDB menu availability check failed.', ['dataset' => $target['dataset']]);
            }
            return null;
        });
        return null === $profile ? null : ['route' => $target['route'], 'parameters' => ['profile' => $profile]];
    }
}
