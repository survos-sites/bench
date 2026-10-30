<?php

declare(strict_types=1);

namespace App\Search;

use Survos\FetchBundle\Contract\PersistentFetcherInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class SearchDatasets
{
    public const PRODUCTS_URL = 'https://dummyjson.com/products?limit=0';

    public function __construct(
        private readonly PersistentFetcherInterface $fetcher,
        #[Autowire('%kernel.project_dir%/data/movie.jsonl')]
        private readonly string $movieFile,
    ) {
    }

    public function load(string $dataset, bool $refresh = false): array
    {
        if ('products' === $dataset) {
            $cached = !$refresh && $this->fetcher->isCached(self::PRODUCTS_URL);
            $response = $this->fetcher->fetch(self::PRODUCTS_URL, ['ttl' => 0, 'force_fetch' => $refresh, 'timeout' => 30.0]);
            if (!$response->isOkay() || null === $response->contents) {
                $this->fetcher->forget(self::PRODUCTS_URL);
                throw new \RuntimeException('DummyJSON product download failed.');
            }
            $payload = json_decode($response->contents, true, flags: JSON_THROW_ON_ERROR);
            $records = $payload['products'] ?? [];
            if ([] === $records || count($records) !== ($payload['total'] ?? null)) {
                throw new \RuntimeException('The product download is empty or incomplete.');
            }

            return ['records' => $records, 'fetchCached' => $cached, 'source' => self::PRODUCTS_URL];
        }
        if ('movies' !== $dataset) {
            throw new \InvalidArgumentException('Choose products or movies.');
        }
        if (!is_file($this->movieFile)) {
            throw new \RuntimeException('Prepare data/movie.jsonl with the existing Castor movie dataset workflow first.');
        }
        $records = [];
        foreach (new \SplFileObject($this->movieFile) as $line) {
            if ('' !== trim($line)) {
                $records[] = json_decode($line, true, flags: JSON_THROW_ON_ERROR);
            }
        }
        if ([] === $records) {
            throw new \RuntimeException('The movie dataset is empty.');
        }

        return ['records' => $records, 'fetchCached' => true, 'source' => 'metarank/msrd movies; sha256:'.hash_file('sha256', $this->movieFile)];
    }

    public static function text(string $dataset, array $record): string
    {
        if ('products' === $dataset) {
            return implode("\n", [$record['title'], $record['description'], 'Brand: '.($record['brand'] ?? ''), 'Category: '.$record['category'], 'Tags: '.implode(', ', $record['tags'] ?? [])]);
        }

        return implode("\n", [
            (string) $record['title'], (string) ($record['overview'] ?? ''),
            'Genres: '.implode(', ', (array) ($record['genres'] ?? [])),
            'Director: '.($record['director'] ?? ''),
            'Cast: '.implode(', ', (array) ($record['actors'] ?? [])),
            'Year: '.($record['year'] ?? ''), 'Tags: '.implode(', ', (array) ($record['tags'] ?? [])),
        ]);
    }
}
