<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class LocalProductSearchController extends AbstractController
{
    #[Route('/demo/products/search', name: 'local_product_search', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $backend = $request->query->getString('backend', 'duckdb');
        if (!in_array($backend, ['duckdb', 'postgres'], true)) {
            throw $this->createNotFoundException('Unknown product search backend.');
        }

        return $this->render('search/products.html.twig', ['backend' => $backend, 'searchName' => 'postgres' === $backend ? 'postgres_products' : 'local_products']);
    }
}
