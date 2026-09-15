<?php

namespace App\Controller;

use App\Entity\Instrument;
use App\Entity\Official;
use Doctrine\ORM\EntityManagerInterface;
use Survos\MeiliBundle\Service\MeiliService;
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
        return $this->redirectToRoute('bench_search', ['code' => 'movie']);
    }

    #[Route('/simple', name: 'app_simple')]
    public function simple(): Response
    {
        return $this->render('app/simple.html.twig', [
            'controllerClass' => self::class
        ]);
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

    #[Route('/wikidata', name: 'app_wikidata')]
    public function wikidata(): Response
    {

        $filename = __DIR__ . '/../../chunk2.gz';
        assert(file_exists($filename), $filename);

//        $handle = gzopen('somefile.gz', 'r');
//        while (!gzeof($handle)) {
//            $buffer = gzgets($handle, 4096);
//            echo $buffer;
//        }
//        gzclose($handle);

        $sfp = gzopen($filename, "r");
        $idx = 0;
        while ($line = fgets($sfp)) {
            if ($idx) {
                $line = trim($line, "\n,");
                if (!json_validate($line)) {
                    break; // because of the partial get

                }
                assert(json_validate($line), $line);
                $data = json_decode($line);
                if ($data->type <> 'item') {
                    dd($data);
                }
                foreach ($data->claims as $claimList) {
                    foreach ($claimList as $claim) {
//                        dd(claim: $claim, qualifiers: $claim->qualifiers);
                        foreach ($claim->qualifiers??[] as $propertyCode => $qualifier) {
//                            dump($propertyCode, $qualifier[0]);
                        }
                    }
                }
//                dd($data, $data->labels->en,$line);
            }
            $idx++;
        }
//        dd($idx . ' records searched');

        return $this->render('app/wikidata.html.twig', [
            'controllerClass' => self::class
        ]);
    }

    #[Route('/grid', name: 'app_grid')]
    public function grid_example(): Response
    {
        return $this->render('app/grid.html.twig', [
            'controllerClass' => self::class
        ]);
    }

}
