<?php

declare(strict_types=1);

namespace App\Tests;

use App\Search\MovieElasticCatalog;
use PHPUnit\Framework\TestCase;

final class MovieElasticCatalogTest extends TestCase
{
    public function testUserTextRemainsLiteralAndFiltersCompose(): void
    {
        $filters = MovieElasticCatalog::filters('A*B?', 'Kurosawa', 'Drama', 'ja', 1950, 1959, 100);
        self::assertSame('*A\\*B\\?*', $filters[0]['wildcard']['title.literal']['value']);
        self::assertTrue($filters[0]['wildcard']['title.literal']['case_insensitive']);
        self::assertSame('*Kurosawa*', $filters[1]['wildcard']['director.literal']['value']);
        self::assertSame(['range'=>['year'=>['gte'=>1950,'lte'=>1959]]], $filters[4]);
        self::assertSame(['range'=>['vote_count'=>['gte'=>100]]], $filters[5]);
        self::assertSame([], MovieElasticCatalog::filters('','','','',null,null,0));
    }
}
