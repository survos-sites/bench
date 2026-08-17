<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Movie;
use App\Schema\MovieSchema;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class MovieController extends AbstractController
{
    /**
     * The controller never touches the graph itself — it hands the entity to
     * MovieSchema, which contributes the nodes, and base.html.twig renders whatever
     * ended up there. Nothing schema-related is passed to the template.
     */
    #[Route('/movie/{id}', name: 'movie_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Movie $movie, Request $request, MovieSchema $schema): Response
    {
        $canonicalUrl = $this->generateUrl(
            'movie_show',
            ['id' => $movie->id],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        $schema->addToGraph($movie, $request->getSchemeAndHttpHost(), $canonicalUrl);

        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
            'canonicalUrl' => $canonicalUrl,
        ]);
    }
}
