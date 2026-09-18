<?php

declare(strict_types=1);

namespace App\Tests\Crawl;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use Doctrine\ORM\Tools\SchemaTool;
use App\Entity\Movie;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CrawlAsVisitorTest extends WebTestCase
{
	#[TestDox('/$method $url ($route)')]
	#[TestWith(['', '/admin/browse', 200])]
	#[TestWith(['', '/admin/messenger', 200])]
	#[TestWith(['', '/admin/messenger/statistics', 200])]
	#[TestWith(['', '/admin/messenger/history', 200])]
	#[TestWith(['', '/admin/messenger/transport', 200])]
	#[TestWith(['', '/admin/messenger/_workers', 200])]
	#[TestWith(['', '/admin/messenger/_transports', 200])]
	#[TestWith(['', '/admin/messenger/_snapshot', 200])]
	#[TestWith(['', '/admin/messenger/_recent-messages', 200])]
	#[TestWith(['', '/', 302])]
	#[TestWith(['', '/dexie', 200])]
	#[TestWith(['', '/search', 200])]
	#[TestWith(['', '/search-template/card', 200])]
	#[TestWith(['', '/congress/crud_index', 200])]
	#[TestWith(['', '/congress/simple_datatables', 200])]
	#[TestWith(['', '/congress/api_grid', 200])]
	#[TestWith(['', '/flysystem_default', 200])]
	#[TestWith(['', '/term/crud/', 200])]
	#[TestWith(['', '/term/crud/new', 200])]
	#[TestWith(['', '/term/crud/browse', 200])]
	#[TestWith(['', '/admin', 200])]
	#[TestWith(['', '/admin/amst', 200])]
	#[TestWith(['', '/admin/amst/new', 403])]
	#[TestWith(['', '/admin/amst/render-filters', 200])]
	#[TestWith(['', '/admin/car', 200])]
	#[TestWith(['', '/admin/car/new', 403])]
	#[TestWith(['', '/admin/car/render-filters', 200])]
	#[TestWith(['', '/admin/image', 200])]
	#[TestWith(['', '/admin/image/new', 403])]
	#[TestWith(['', '/admin/image/render-filters', 200])]
	#[TestWith(['', '/admin/instrument', 200])]
	#[TestWith(['', '/admin/instrument/new', 403])]
	#[TestWith(['', '/admin/instrument/render-filters', 200])]
	#[TestWith(['', '/admin/jeopardy', 200])]
	#[TestWith(['', '/admin/jeopardy/new', 403])]
	#[TestWith(['', '/admin/jeopardy/render-filters', 200])]
	#[TestWith(['', '/admin/marvel', 200])]
	#[TestWith(['', '/admin/marvel/new', 403])]
	#[TestWith(['', '/admin/marvel/render-filters', 200])]
	#[TestWith(['', '/admin/movie', 200])]
	#[TestWith(['', '/admin/movie/new', 403])]
	#[TestWith(['', '/admin/movie/render-filters', 200])]
	#[TestWith(['', '/admin/official', 200])]
	#[TestWith(['', '/admin/official/new', 403])]
	#[TestWith(['', '/admin/official/render-filters', 200])]
	#[TestWith(['', '/admin/product', 200])]
	#[TestWith(['', '/admin/product/new', 403])]
	#[TestWith(['', '/admin/product/render-filters', 200])]
	#[TestWith(['', '/admin/wam', 200])]
	#[TestWith(['', '/admin/wam/new', 200])]
	#[TestWith(['', '/admin/wam/render-filters', 200])]
	#[TestWith(['', '/admin/wcma', 200])]
	#[TestWith(['', '/admin/wcma/new', 403])]
	#[TestWith(['', '/admin/wcma/render-filters', 200])]
	#[TestWith(['', '/admin/wine', 200])]
	#[TestWith(['', '/admin/wine/new', 403])]
	#[TestWith(['', '/admin/wine/render-filters', 200])]
	#[TestWith(['', '/js/routing', 200])]
	#[TestWith(['', '/auth/login', 200])]
	#[TestWith(['', '/auth/profile', 401])]
	#[TestWith(['', '/auth/providers', 200])]
	#[TestWith(['', '/auth/register', 200])]
	#[TestWith(['', '/crawler/crawlerdata', 200])]
	#[TestWith(['', '/admin/commands/_command/processes', 200])]
	#[TestWith(['', '/admin/commands/_command', 200])]
	#[TestWith(['', '/state/', 200])]
	#[TestWith(['', '/state/entities', 200])]
	#[TestWith(['', '/state/_state/workflows', 200])]
	#[TestWith(['', '/debug-menu', 200])]
	#[TestWith(['', '/docs', 200])]
	#[TestWith(['', '/favicon.svg', 200])]
	#[TestWith(['', '/health', 200])]
	#[TestWith(['', '/entity-constants', 200])]
	#[TestWith(['', '/routes-sitemap', 200])]
	#[TestWith(['', '/routes-sitemap.txt', 200])]
	#[TestWith(['', '/routes-sitemap.json', 200])]
	#[TestWith(['', '/imgproxy/url', 404])]
	#[TestWith(['', '/imgproxy/info', 404])]
	#[TestWith(['', '/admin/elastic/', 200])]
	#[TestWith(['', '/admin/browse?debugMenuSlots=1', 200])]
	#[TestWith(['', '/state/workflow/OfficialWorkflow', 200])]
	#[TestWith(['', '/admin/browse/media_photo', 200])]
	#[TestWith(['', '/admin/browse/media_video', 200])]
	#[TestWith(['', '/entity/app_car/search', 200])]
	#[TestWith(['', '/entity/app_marvel/search', 200])]
	#[TestWith(['', '/entity/app_movie/search', 200])]
	#[TestWith(['', '/entity/app_wcma/search', 200])]
	#[TestWith(['', '/search/car', 200])]
	#[TestWith(['', '/api/cars', 200])]
	#[TestWith(['', '/entity/app_car', 200])]
	#[TestWith(['', '/search/marvel', 200])]
	#[TestWith(['', '/api/marvels', 200])]
	#[TestWith(['', '/entity/app_marvel', 200])]
	#[TestWith(['', '/search/movie', 200])]
	#[TestWith(['', '/api/movies', 200])]
	#[TestWith(['', '/entity/app_movie', 200])]
	#[TestWith(['', '/search/wcma', 200])]
	#[TestWith(['', '/api/wcmas', 200])]
	#[TestWith(['', '/entity/app_wcma', 200])]
	#[TestWith(['', '/admin/messenger/transport/official.fetch.wiki', 200])]
	#[TestWith(['', '/admin/messenger/transport/elastic', 200])]
	#[TestWith(['', '/admin/messenger/transport/failed', 200])]
	#[TestWith(['', '/admin/messenger/history?transport=official.fetch.wiki', 200])]
	#[TestWith(['', '/admin/messenger/history?transport=elastic', 200])]
	#[TestWith(['', '/admin/messenger/history?type=Survos%5CElasticBundle%5CMessage%5CReindexDocuments', 200])]
	#[TestWith(['', '/admin/messenger/history/3', 200])]
	#[TestWith(['', '/admin/messenger/history/2', 200])]
	#[TestWith(['', '/admin/messenger/history/1', 200])]
	#[TestWith(['', '/admin/messenger/statistics?period=in-last-hour', 200])]
	#[TestWith(['', '/admin/messenger/statistics?period=in-last-week', 200])]
	#[TestWith(['', '/admin/messenger/statistics?period=in-last-month', 200])]
	#[TestWith(['', '/search?debugMenuSlots=1', 200])]
	#[TestWith(['', '/dexie?debugMenuSlots=1', 200])]
	#[TestWith(['', '/congress/crud_index?debugMenuSlots=1', 200])]
	#[TestWith(['', '/congress/simple_datatables?debugMenuSlots=1', 200])]
	#[TestWith(['', '/congress/api_grid?debugMenuSlots=1', 200])]
	#[TestWith(['', '/flysystem_default?debugMenuSlots=1', 200])]
	#[TestWith(['', '/term/crud/?debugMenuSlots=1', 200])]
	#[TestWith(['', '/term/crud/new?debugMenuSlots=1', 200])]
	#[TestWith(['', '/term/crud/browse?debugMenuSlots=1', 200])]
	#[TestWith(['', '/admin?routeName=bench_search_index', 200])]
	#[TestWith(['', '/admin?routeName=app_homepage', 302])]
	#[TestWith(['', '/admin/car?page=1&sort%5BdimensionsHeight%5D=DESC', 200])]
	#[TestWith(['', '/admin/car?page=1&sort%5BdimensionsLength%5D=DESC', 200])]
	#[TestWith(['', '/admin/car?page=1&sort%5BdimensionsWidth%5D=DESC', 200])]
	#[TestWith(['', '/admin/car/1', 200])]
	#[TestWith(['', '/admin/car/2', 200])]
	#[TestWith(['', '/admin/car/3', 200])]
	#[TestWith(['', '/admin/car/4', 200])]
	#[TestWith(['', '/admin/marvel?page=1&sort%5Bname%5D=DESC', 200])]
	#[TestWith(['', '/admin/marvel?page=1&sort%5BsuperName%5D=DESC', 200])]
	#[TestWith(['', '/admin/marvel?page=1&sort%5Bcode%5D=DESC', 200])]
	#[TestWith(['', '/admin/marvel/8-Ball', 200])]
	#[TestWith(['', '/admin/marvel/Abdul-Alhazred', 200])]
	#[TestWith(['', '/admin/marvel/Abigail-Brand', 200])]
	#[TestWith(['', '/admin/marvel/Abner-Jenkins', 200])]
	#[TestWith(['', '/admin/movie?page=1&sort%5Bid%5D=DESC', 200])]
	#[TestWith(['', '/admin/movie?page=1&sort%5Btitle%5D=DESC', 200])]
	#[TestWith(['', '/admin/movie?page=1&sort%5Bdirector%5D=DESC', 200])]
	#[TestWith(['', '/admin/movie/5', 200])]
	#[TestWith(['', '/admin/movie/11', 200])]
	#[TestWith(['', '/admin/movie/12', 200])]
	#[TestWith(['', '/admin/movie/13', 200])]
	#[TestWith(['', '/admin/wcma?page=1&sort%5Bid%5D=DESC', 200])]
	#[TestWith(['', '/admin/wcma?page=1&sort%5BaccessionNumber%5D=DESC', 200])]
	#[TestWith(['', '/admin/wcma?page=1&sort%5Bmaker%5D=DESC', 200])]
	#[TestWith(['', '/admin/wcma/1', 200])]
	#[TestWith(['', '/admin/wcma/2', 200])]
	#[TestWith(['', '/admin/wcma/3', 200])]
	#[TestWith(['', '/admin/wcma/4', 200])]
	#[TestWith(['', '/auth/login?debugMenuSlots=1', 200])]
	#[TestWith(['', '/auth/providers?debugMenuSlots=1', 200])]
	#[TestWith(['', '/auth/provider/amazon', 200])]
	#[TestWith(['', '/auth/provider/auth0', 200])]
	#[TestWith(['', '/auth/provider/azure', 200])]
	#[TestWith(['', '/auth/provider/bitbucket', 200])]
	#[TestWith(['', '/auth/register?debugMenuSlots=1', 200])]
	#[TestWith(['', '/crawler/crawlerdata?debugMenuSlots=1', 200])]
	#[TestWith(['', '/admin/commands/_command/processes?status=pending', 200])]
	#[TestWith(['', '/admin/commands/_command/processes?status=running', 200])]
	#[TestWith(['', '/admin/commands/_command/processes?status=succeeded', 200])]
	#[TestWith(['', '/admin/commands/_command/run/app:jeopardy', 200])]
	#[TestWith(['', '/admin/commands/_command/run/app:music', 200])]
	#[TestWith(['', '/admin/commands/_command/run/app:screenshots', 200])]
	#[TestWith(['', '/admin/commands/_command/run/app:tmdb', 200])]
	#[TestWith(['', '/state/?debugMenuSlots=1', 200])]
	#[TestWith(['', '/state/entities?debugMenuSlots=1', 200])]
	#[TestWith(['', '/state/_state/workflows?debugMenuSlots=1', 200])]
	#[TestWith(['', '/debug-menu?debugMenuSlots=1', 200])]
	#[TestWith(['', '/entity-constants?debugMenuSlots=1', 200])]
	#[TestWith(['', '/routes-sitemap?debugMenuSlots=1', 200])]
	#[TestWith(['', '/admin/elastic/?debugMenuSlots=1', 200])]
	#[TestWith(['', '/admin/elastic/app_car', 200])]
	#[TestWith(['', '/admin/elastic/app_marvel', 200])]
	#[TestWith(['', '/admin/elastic/app_movie', 200])]
	#[TestWith(['', '/admin/elastic/app_wcma', 200])]
	#[TestWith(['', '/state/workflow/OfficialWorkflow?debugMenuSlots=1', 200])]
	#[TestWith(['', '/state/workflow/OfficialWorkflow?states=%22new%22', 200])]
	#[TestWith(['', '/state/workflow/OfficialWorkflow?states=%22details%22', 200])]
	#[TestWith(['', '/movie/5', 200])]
	#[TestWith(['', '/movie/11', 200])]
	#[TestWith(['', '/movie/12', 200])]
	#[TestWith(['', '/movie/13', 200])]
	#[TestWith(['', '/admin?routeName=bench_search_index&debugMenuSlots=1', 200])]
	#[TestWith(['', '/docs?debugMenuSlots=1', 200])]
	#[TestWith(['', '/docs/elasticsearch', 200])]
	#[TestWith(['', '/docs/search-baseline', 200])]
	public function testRoute(string $username, string $url, string|int|null $expected): void
	{
		$client = self::createClient();
        $client->disableReboot();
        $em = self::getContainer()->get('doctrine.orm.entity_manager');
        self::assertTrue($em->getConnection()->getParams()['memory'] ?? false);
        (new SchemaTool($em))->createSchema($em->getMetadataFactory()->getAllMetadata());
        foreach ([5, 11, 12, 13] as $id) {
            $movie = new Movie();
            $movie->id = $id;
            $movie->title = 'Fixture movie '.$id;
            $em->persist($movie);
        }
        foreach ([1, 2, 3, 4] as $id) {
            $car = new \App\Entity\Car();
            $car->id = $id;
            $car->identificationId = 'Fixture car '.$id;
            $em->persist($car);
            $artwork = new \App\Entity\Wcma();
            $artwork->id = $id;
            $em->persist($artwork);
        }
        foreach (['8-Ball', 'Abdul-Alhazred', 'Abigail-Brand', 'Abner-Jenkins'] as $code) {
            $hero = new \App\Entity\Marvel();
            $hero->code = $code;
            $hero->name = $code;
            $em->persist($hero);
        }
        for ($i = 0; $i < 3; ++$i) {
            $stamp = (new \Zenstruck\Messenger\Monitor\Stamp\MonitorStamp())->markReceived('elastic')->markFinished();
            $envelope = new \Symfony\Component\Messenger\Envelope(new \stdClass(), [$stamp]);
            $em->persist(new \App\Entity\ProcessedMessage($envelope, new \Zenstruck\Messenger\Monitor\History\Model\Results([])));
        }
        $em->flush();
        $client->request('GET', $url);
        self::assertResponseStatusCodeSame((int) $expected);
	}
}
