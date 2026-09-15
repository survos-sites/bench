<?php

namespace App\Controller;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use App\Entity\Official;
use App\Form\OfficialType;
use App\Repository\OfficialRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Survos\WikiBundle\Service\WikiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/congress')]
class CongressController extends AbstractController
{

    public function __construct()
    {
    }

    #[Route('/crud_index', name: 'congress_crud_index', methods: ['GET'], options: ['label' => ['en' => 'Simple Datatables']])]
    public function congressSimpleDatatables(OfficialRepository $officialRepository): Response
    {
        return $this->render('congress/simple_datatables.html.twig', [
            'officials' => $officialRepository->findAll(),
            'useStimulus' => false
        ]);
    }

    #[Route('/simple_datatables', methods: ['GET'])]
    public function simple_datatables(OfficialRepository $officialRepository): Response
    {
        return $this->render('congress/simple_datatables.html.twig', [
            'officials' => $officialRepository->findAll(),
        ]);
    }


    #[Route('/api_grid',  name: 'congress_api_grid', methods: ['GET'], options: ['label' => "Browse (api_grid)"])]
    public function api_grid(): Response
    {
        return $this->render('congress/browse.html.twig', [
            'class' => Official::class,
        ]);
    }


    #[Route('/{id}', name: 'app_congress_show', methods: ['GET'])]
    public function show(Official $official, #[Target('defaultStorage')] FilesystemOperator $defaultStorage): Response
    {
//        foreach ($official->getImageCodes() as $imageCodeData) {
//            $path = $imageCodeData['code'];
//            // or fileExists?
//            $mimeType = $defaultStorage->mimeType($path);
//            $image = $defaultStorage->has($path);
////            dd($image, $mimeType);
//        }
        return $this->render('congress/show.html.twig', [
            'official' => $official,
        ]);
    }
    #[Route('/{id}/refresh', name: 'congress_refresh', methods: ['GET', 'POST'])]
    public function refresh(Request $request, Official $official,
                            WikiService $wikiService,
                            EntityManagerInterface $entityManager,
                            CacheManager $cacheManager): Response
    {
        foreach ($official->getImageCodes()??[] as $imageData) {
            $cacheManager->remove($imageData['code']);
        }
        $wikiData = $wikiService->fetchWikidataPage($official->getWikidataId());
        $official->setWikiData($wikiData->toArray());
        $entityManager->flush();

        return $this->redirectToRoute('app_congress_show', $official->getrp());


    }

}
