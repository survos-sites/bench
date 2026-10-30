<?php

declare(strict_types=1);

namespace App\Controller;

use App\Search\DuckDbSearch;
use App\Search\EmbeddingProfiles;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DuckDbDemoController extends AbstractController
{
    #[Route('/demo/product-duckdb', name: 'demo_product_duckdb', defaults: ['dataset' => 'products'], methods: ['GET'])]
    #[Route('/demo/movie-duckdb', name: 'demo_movie_duckdb', defaults: ['dataset' => 'movies'], methods: ['GET'])]
    public function __invoke(Request $request, string $dataset, DuckDbSearch $search, EmbeddingProfiles $profiles, LoggerInterface $logger): Response
    {
        $query = trim($request->query->getString('q'));
        $category = $request->query->getString('category');
        $profile = $request->query->getString('profile', $profiles->default());
        if (!in_array($profile, ['openai', 'nomic'], true)) {
            throw $this->createNotFoundException('Unknown embedding profile.');
        }
        $snapshot = $result = $error = null;
        try {
            if (!in_array('duckdb', \PDO::getAvailableDrivers(), true)) {
                throw new \RuntimeException('The PDO DuckDB driver is not installed in this runtime.');
            }
            $snapshot = $search->snapshot($dataset, $profile);
            if (strlen($query) > 512) {
                $error = 'Please keep your search under 512 characters.';
            } elseif ('' !== $query && null !== $snapshot) {
                $result = $search->search($dataset, $profile, $query, $category, $snapshot);
            }
        } catch (\Throwable $exception) {
            $logger->error('Remote DuckDB search failed.', ['exception' => $exception]);
            $error = in_array('duckdb', \PDO::getAvailableDrivers(), true)
                ? 'Search is temporarily unavailable. Check S3 access and the embedding provider configuration.'
                : 'Remote search is not enabled on this deployment: the PDO DuckDB driver is missing.';
        }
        $suggestions = 'products' === $dataset
            ? ['gifts for a techie', 'something for a camping trip', 'romantic gifts', 'a laptop for work']
            : ['a lonely astronaut far from home', 'a romantic comedy involving mistaken identity', 'a tense courtroom drama', 'an adventure the whole family can watch'];

        return $this->render('demo/product_duckdb.html.twig', compact('query', 'category', 'profile', 'dataset', 'snapshot', 'result', 'error', 'suggestions'));
    }
}
