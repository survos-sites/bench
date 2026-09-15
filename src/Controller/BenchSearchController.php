<?php

declare(strict_types=1);

namespace App\Controller;

use Survos\SearchBundle\Search\SearchProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BenchSearchController extends AbstractController
{
    private const array COLLECTIONS = [
        'movie' => ['label' => 'Movies', 'description' => 'Discover a new favorite. Explore stories, genres, and the people behind them.', 'facets' => ['genres' => 'Genre', 'actors' => 'Cast', 'tags' => 'Tags'], 'ranges' => ['year' => 'Release year', 'votes' => 'Votes']],
        'car' => ['label' => 'Cars', 'description' => 'Find your next drive. Compare makes, transmissions, and performance.', 'facets' => ['identificationMake' => 'Make', 'identificationClassification' => 'Transmission', 'fuelInformationFuelType' => 'Fuel'], 'ranges' => ['identificationYear' => 'Model year']],
        'marvel' => ['label' => 'Marvel', 'description' => 'Heroes, villains, and everyone in between. Explore the Marvel universe.', 'facets' => [], 'ranges' => []],
        'wcma' => ['label' => 'Museum collection', 'description' => 'Explore art across cultures, materials, and time.', 'facets' => ['classification' => 'Classification', 'department' => 'Department', 'culture' => 'Culture'], 'ranges' => []],
    ];

    #[Route('/search/{code}', name: 'bench_search', defaults: ['code' => 'movie'], methods: ['GET'])]
    public function browse(string $code, SearchProvider $provider): Response
    {
        $collection = self::COLLECTIONS[$code] ?? throw $this->createNotFoundException();
        $name = 'app_'.$code;
        $search = $provider->getSearch($name)->create();
        $available = array_map(static fn ($facet) => $facet->getProperty(), $search->getFacets());
        $collection['facets'] = array_intersect_key($collection['facets'], array_flip($available));
        $collection['ranges'] = array_intersect_key($collection['ranges'], array_flip($available));
        if ($code === 'marvel') {
            foreach (array_slice($available, 0, 4) as $field) { $collection['facets'][$field] = ucfirst($field); }
        }
        $sorts = array_map(static fn ($sort) => ['value' => $name.'::'.$sort->getKey(), 'label' => $sort->getLabel()], $search->getAvailableSorts());
        return $this->render('search/browse.html.twig', compact('code', 'name', 'collection', 'sorts'));
    }

    #[Route('/search-template/card', name: 'bench_search_template', methods: ['GET'])]
    public function template(): Response
    {
        // Send source: each hit is rendered by twig-browser, with FOS path() support.
        return new Response(file_get_contents($this->getParameter('kernel.project_dir').'/templates/search/card.browser.twig'), 200, ['Content-Type' => 'text/plain; charset=utf-8', 'Cache-Control' => 'public, max-age=300']);
    }
}
