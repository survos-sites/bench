<?php

declare(strict_types=1);

namespace App\Search;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Survos\SearchBundle\Service\ElasticIndexNameResolver;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/** Uses the same DSN and name resolver as the browsing/indexing adapter. */
final class MovieElasticClient
{
    private ?Client $client = null;

    public function __construct(
        #[Autowire('%env(ELASTICSEARCH_DSN)%')] #[\SensitiveParameter] private readonly string $dsn,
        private readonly ElasticIndexNameResolver $names,
    ) {}

    public function index(): string { return $this->names->uidForRaw('movie_snapshot_2025'); }

    public function search(array $body): array
    {
        return $this->native()->search(['index'=>$this->index(), 'body'=>$body])->asArray();
    }

    public function native(): Client
    {
        $parts = parse_url($this->dsn);
        if (!$parts || !isset($parts['host'])) { throw new \RuntimeException('Invalid Elasticsearch configuration.'); }
        if (null === $this->client) {
            $scheme = 'elasticsearch+https' === ($parts['scheme'] ?? '') ? 'https' : 'http';
            $builder = ClientBuilder::create()->setHosts([$scheme.'://'.$parts['host'].':'.($parts['port'] ?? 9200)]);
            parse_str($parts['query'] ?? '', $query);
            if (!empty($query['ca'])) { $builder->setCABundle($query['ca']); }
            if (!empty($query['api_key'])) { $builder->setApiKey($query['api_key']); }
            elseif (isset($parts['user'])) { $builder->setBasicAuthentication(rawurldecode($parts['user']), rawurldecode($parts['pass'] ?? '')); }
            $this->client = $builder->build();
        }
        return $this->client;
    }
}
