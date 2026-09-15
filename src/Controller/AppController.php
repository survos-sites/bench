<?php

namespace App\Controller;

use App\Entity\Instrument;
use App\Entity\Official;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{

    #[Route(path: '/', name: 'app_homepage', options: ['sitemap' => ['priority' => 1]])]
    public function homepage(): Response
    {
        return $this->redirectToRoute('bench_search_index');
    }

    #[Route('/test-webhook/{id}', name: 'app_webhook')]
    public function webhook(Request $request, Official $official, EntityManagerInterface $em): Response
    {
        // best practice: push this message to a queue and handle elsewhere

        // update the official images block with
        $images = $official->getImageCodes();
        $data = $request->request->all(); // it's a post

        $images[$data['path']] = $data['filters']??[];
        $official->setImageCodes($images);
        $em->flush();
        // update the database with available filters.  This probably means the images need to move to their own database, or attach metadata to the resize request
        return new Response(json_encode($official->getImageCodes(), JSON_PRETTY_PRINT+ JSON_UNESCAPED_SLASHES));
    }

    #[Route('/dexie', name: 'app_dexie')]
    public function dexie(): Response
    {
        return $this->render('app/dexie2.html.twig', [
            'controllerClass' => self::class
        ]);
    }


}
