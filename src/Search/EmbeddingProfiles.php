<?php

declare(strict_types=1);

namespace App\Search;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class EmbeddingProfiles
{
    public function __construct(
        private readonly OpenAiEmbedder $openai,
        private readonly NomicEmbedder $nomic,
        #[Autowire('%env(DUCKDB_EMBEDDING_PROFILE)%')]
        private readonly string $defaultProfile,
    ) {
    }

    public function default(): string
    {
        return $this->defaultProfile;
    }

    public function get(string $profile): EmbedderInterface
    {
        return match ($profile) {
            'openai' => $this->openai,
            'nomic' => $this->nomic,
            default => throw new \InvalidArgumentException('Choose the openai or nomic embedding profile.'),
        };
    }
}
