<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MovieBrowseController extends AbstractController
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')] private readonly string $projectDir,
    ) {}

    #[Route('/movies/search', name: 'movie_browse', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('movies/browse.html.twig');
    }

    #[Route('/movies/search/card', name: 'movie_browse_card', methods: ['GET'])]
    public function card(): Response
    {
        return new Response(file_get_contents($this->projectDir.'/templates/movies/card.browser.twig'), headers: ['Content-Type'=>'text/plain; charset=UTF-8']);
    }
}
