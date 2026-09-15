<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class FunctionalTest extends WebTestCase
{
    public function testSearchTemplateIsSentAsBrowserTwigSource(): void
    {
        $client = self::createClient();
        $client->request('GET', '/search-template/card');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'text/plain; charset=utf-8');
        self::assertStringContainsString("path('movie_show'", $client->getResponse()->getContent());
    }

    public function testUnexposedSearchIsRejectedBeforeContactingAnEngine(): void
    {
        $client = self::createClient();
        $client->jsonRequest('POST', '/instant-search', ['requests' => [['indexName' => 'private_index']]]);
        self::assertResponseStatusCodeSame(400);
    }
}
