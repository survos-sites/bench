<?php

declare(strict_types=1);

namespace App\Controller;

use App\Search\DuckDbProductSearch;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DuckDbDemoController extends AbstractController
{
    #[Route('/demo/product-duckdb', name: 'demo_product_duckdb', methods: ['GET'])]
    public function __invoke(Request $request, DuckDbProductSearch $search, LoggerInterface $logger): Response
    {
        $query = trim($request->query->getString('q'));
        $category = $request->query->getString('category');
        $snapshot = $search->snapshot();
        $result = null;
        $error = null;
        if (strlen($query) > 512) {
            $error = 'Please keep your search under 512 characters.';
        } elseif ('' !== $query && null !== $snapshot) {
            try {
                $result = $search->search($query, $category);
            } catch (\Throwable $exception) {
                $logger->error('DuckDB product search failed.', ['exception' => $exception]);
                $error = 'Search is unavailable. Check that local Ollama is running with the same Nomic model used to build this snapshot.';
            }
        }
        return $this->render('demo/product_duckdb.html.twig', compact('query', 'category', 'snapshot', 'result', 'error'));
    }
}
