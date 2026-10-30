<?php

declare(strict_types=1);

namespace App\Search;

use Survos\FetchBundle\Cache\SqliteCachePoolFactory;
use Symfony\AI\Platform\PlatformInterface;
use Symfony\AI\Platform\Result\VectorResult;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class OpenAiEmbedder implements EmbedderInterface
{
    public function __construct(
        #[Autowire(service: 'ai.platform.openai')]
        private readonly PlatformInterface $platform,
        #[Autowire('%kernel.project_dir%/var/data/openai-embedding-cache.db')]
        private readonly string $cachePath,
        #[Autowire('%env(DUCKDB_OPENAI_MODEL)%')]
        private readonly string $modelName,
        #[Autowire('%env(int:DUCKDB_OPENAI_DIMENSIONS)%')]
        private readonly int $dimensionCount,
    ) {
    }

    public function model(): string
    {
        return $this->modelName;
    }

    public function dimensions(): int
    {
        return $this->dimensionCount;
    }

    public function digest(): string
    {
        return 'openai:'.$this->modelName.':'.$this->dimensionCount.':raw-text-v1';
    }

    public function embed(array $texts, string $digest, bool $query = false): array
    {
        if ($digest !== $this->digest()) {
            throw new \RuntimeException('OpenAI model configuration does not match the snapshot.');
        }
        $cache = SqliteCachePoolFactory::createPool($this->cachePath);
        $vectors = $missing = $items = [];
        foreach ($texts as $i => $text) {
            if ('' === trim($text)) {
                throw new \InvalidArgumentException('Cannot embed empty text.');
            }
            $items[$i] = $cache->getItem(hash('sha256', $digest."\0".$text));
            if ($items[$i]->isHit()) {
                $vectors[$i] = $items[$i]->get();
            } else {
                $missing[$i] = $text;
            }
        }
        if ([] !== $missing) {
            $result = $this->platform->invoke($this->modelName, array_values($missing), ['dimensions' => $this->dimensionCount])->getResult();
            if (!$result instanceof VectorResult || count($result->getContent()) !== count($missing)) {
                throw new \RuntimeException('OpenAI returned an unexpected embedding result.');
            }
            foreach (array_keys($missing) as $position => $i) {
                $vector = $result->getContent()[$position]->getData();
                if (count($vector) !== $this->dimensionCount) {
                    throw new \RuntimeException('OpenAI embedding dimensions do not match the configuration.');
                }
                $vectors[$i] = $vector;
                $cache->save($items[$i]->set($vector));
            }
        }
        ksort($vectors);

        return array_values($vectors);
    }
}
