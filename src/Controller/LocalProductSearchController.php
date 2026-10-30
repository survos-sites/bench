<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LocalProductSearchController extends AbstractController
{
    #[Route('/demo/products/search', name: 'local_product_search', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->render('search/products.html.twig');
    }
}
