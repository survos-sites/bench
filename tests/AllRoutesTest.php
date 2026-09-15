<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AllRoutesTest extends WebTestCase
{
    public function testHomeOpensBrowserSearch(): void
    {
        self::createClient()->request('GET', '/');
        self::assertResponseRedirects('/search');
    }
}
