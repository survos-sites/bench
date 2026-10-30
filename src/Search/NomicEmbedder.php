<?php

declare(strict_types=1);

namespace App\Search;

use Survos\FetchBundle\Cache\SqliteCachePoolFactory;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class NomicEmbedder implements EmbedderInterface
{
    public const MODEL = 'nomic-embed-text';
    public const DIMENSIONS = 768;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        #[Autowire('%kernel.project_dir%/var/data/nomic-cache.db')]
        private readonly string $cachePath,
        #[Autowire('%env(OLLAMA_HOST)%')]
        private readonly string $host,
    ) {
    }

    public function digest(): string
    {
        $models = $this->httpClient->request('GET', rtrim($this->host, '/').'/api/tags', ['timeout' => 10, 'max_duration' => 15])->toArray()['models'] ?? [];
        foreach ($models as $model) {
            if (in_array($model['name'], [self::MODEL, self::MODEL.':latest'], true)) {
                return $model['digest'];
            }
        }
        throw new \RuntimeException('Install nomic-embed-text in Ollama before indexing or searching.');
    }

    public function model(): string
    {
        return self::MODEL;
    }

    public function dimensions(): int
    {
        return self::DIMENSIONS;
    }

    /**
     * @param list<string> $texts
     *
     * @return list<list<float>>
     */
    public function embed(array $texts, string $digest, bool $query = false): array
    {
        $inputs = array_map(static fn (string $text): string => ($query ? 'search_query: ' : 'search_document: ').$text, $texts);
        $key = hash('sha256', json_encode([$digest, $inputs], JSON_THROW_ON_ERROR));
        $cache = SqliteCachePoolFactory::createPool($this->cachePath);

        return $cache->get($key, function () use ($inputs): array {
            $result = $this->httpClient->request('POST', rtrim($this->host, '/').'/api/embed', [
                'json' => ['model' => self::MODEL, 'input' => $inputs, 'truncate' => false],
                'timeout' => 120, 'max_duration' => 180,
            ])->toArray();
            $vectors = $result['embeddings'] ?? [];
            if (count($vectors) !== count($inputs)) {
                throw new \RuntimeException('Ollama returned an unexpected number of embeddings.');
            }
            foreach ($vectors as $vector) {
                if (self::DIMENSIONS !== count($vector)) {
                    throw new \RuntimeException('Nomic must return 768-dimensional vectors.');
                }
            }

            return $vectors;
        });
    }
}
