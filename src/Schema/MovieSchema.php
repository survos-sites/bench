<?php

declare(strict_types=1);

namespace App\Schema;

use App\Entity\Movie;
use Spatie\SchemaOrg\AggregateRating;
use Spatie\SchemaOrg\ImageObject;
use Spatie\SchemaOrg\Movie as MovieNode;
use Spatie\SchemaOrg\Person;
use Spatie\SchemaOrg\Schema;
use Spatie\SchemaOrg\WebPage;
use Spatie\SchemaOrg\WebSite;
use Survos\SchemaOrgBundle\Graph\SchemaOrgGraph;

/**
 * Hand-written Movie → JSON-LD mapping: the Phase 1 spike for
 * survos/schema-org-bundle (survos-sites/bench#5).
 *
 * Deliberately hand-written. Phase 2 replaces this class with
 * #[SchemaOrg]/#[SchemaProperty] attributes on the entity, and this file is the
 * reference output that mapping has to reproduce — so it maps a real entity with
 * real awkwardness (rating as a string column, actors as a JSON array of names,
 * a poster URL and nothing else about the image).
 */
final readonly class MovieSchema
{
    public function __construct(
        private SchemaOrgGraph $schemaOrg,
    ) {
    }

    public function addToGraph(Movie $movie, string $siteUrl, string $canonicalUrl): void
    {
        $siteUrl = rtrim($siteUrl, '/');
        $movieId = $canonicalUrl . '#movie';

        // Site-wide, so getOrCreate rather than add: every page contributes the same
        // WebSite node and the last one must not clobber a fuller earlier version.
        $website = $this->schemaOrg->getOrCreate(WebSite::class, $siteUrl . '/#website');
        $website
            ->identifier($siteUrl . '/#website')
            ->url($siteUrl)
            ->name('SearchBench');

        $movieNode = Schema::movie()
            ->identifier($movieId)
            ->url($canonicalUrl);

        if ($this->hasText($movie->title)) {
            $movieNode->name(trim($movie->title));
        }

        if ($this->hasText($movie->overview)) {
            $movieNode->description(trim($movie->overview));
        }

        if (null !== $movie->year) {
            $movieNode->dateCreated((string) $movie->year);
        }

        $genres = $this->cleanStrings($movie->genres);
        if ([] !== $genres) {
            $movieNode->genre($genres);
        }

        if ($this->hasText($movie->director)) {
            $movieNode->director($this->person($siteUrl, $movie->director)->referenced());
        }

        $actors = array_map(
            fn (string $actor) => $this->person($siteUrl, $actor)->referenced(),
            $this->cleanStrings($movie->actors),
        );
        if ([] !== $actors) {
            $movieNode->actor($actors);
        }

        $this->addPoster($movie, $movieNode, $movieId);
        $this->addRating($movie, $movieNode, $movieId);

        $webPageId = $canonicalUrl . '#webpage';
        $webPage = $this->schemaOrg->getOrCreate(WebPage::class, $webPageId);
        $webPage
            ->identifier($webPageId)
            ->url($canonicalUrl)
            ->isPartOf($website->referenced())
            ->mainEntity($movieNode->referenced());

        if ($this->hasText($movie->title)) {
            $webPage->name(trim($movie->title));
        }

        // Both directions, via referenced() — the @id link, not a nested copy of the
        // node. Embedding either one inside the other would duplicate it in the graph.
        $movieNode->mainEntityOfPage($webPage->referenced());

        $this->schemaOrg->add($movieNode);
    }

    private function addPoster(Movie $movie, MovieNode $movieNode, string $movieId): void
    {
        if (!$this->hasText($movie->posterUrl)) {
            return;
        }

        $posterId = $movieId . '-poster';
        $image = $this->schemaOrg->getOrCreate(ImageObject::class, $posterId);
        $image
            ->identifier($posterId)
            ->contentUrl(trim($movie->posterUrl));

        if ($this->hasText($movie->title)) {
            $image->name(trim($movie->title) . ' poster');
        }

        $movieNode->image($image->referenced());
    }

    /**
     * Skipped unless there is a real rating AND at least one vote: Google rejects an
     * AggregateRating with ratingCount 0, and `rating` is a string column that has
     * held junk, so it is validated rather than cast.
     */
    private function addRating(Movie $movie, MovieNode $movieNode, string $movieId): void
    {
        $rating = filter_var($movie->rating, \FILTER_VALIDATE_FLOAT);
        if (false === $rating || $rating < 0.0 || $rating > 10.0) {
            return;
        }

        if (null === $movie->votes || $movie->votes <= 0) {
            return;
        }

        $ratingId = $movieId . '-rating';
        $aggregateRating = $this->schemaOrg->getOrCreate(AggregateRating::class, $ratingId);
        $aggregateRating
            ->identifier($ratingId)
            ->ratingValue($rating)
            ->ratingCount($movie->votes)
            ->worstRating(0)
            ->bestRating(10);

        $movieNode->aggregateRating($aggregateRating->referenced());
    }

    /**
     * One node per person, keyed by the URL that identifies them.
     *
     * getOrCreate() on that URL is what makes the director-who-also-acts case
     * collapse into a single Person instead of two nodes with the same @id. The
     * source has only a name, so the slugged name is the natural key — nothing is
     * hashed, because a hash would be unresolvable and would change if we ever
     * changed the algorithm.
     */
    private function person(string $siteUrl, string $name): Person
    {
        $name = trim($name);
        $personId = $siteUrl . '/people/' . rawurlencode(mb_strtolower($name));

        $person = $this->schemaOrg->getOrCreate(Person::class, $personId);

        return $person
            ->identifier($personId)
            ->name($name);
    }

    /**
     * @param array<array-key, mixed>|null $values
     *
     * @return list<string>
     */
    private function cleanStrings(?array $values): array
    {
        if (null === $values) {
            return [];
        }

        $strings = array_map(
            static fn (string $value): string => trim($value),
            array_filter($values, static fn (mixed $value): bool => \is_string($value) && '' !== trim($value)),
        );

        return array_values(array_unique($strings));
    }

    private function hasText(?string $value): bool
    {
        return null !== $value && '' !== trim($value);
    }
}
