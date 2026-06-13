<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\CategoryEntity;

use App\Cataloging\Service\CatalogCollectionRuleEngineService;
use PHPUnit\Framework\TestCase;

final class CategoryRuleEngineTest extends TestCase
{
    public function testMatchSupportsNullAsAnExplicitValue(): void
    {
        $engine = new CatalogCollectionRuleEngineService();

        self::assertTrue($engine->match(
            ['slug' => null, 'locale' => 'en'],
            ['slug' => null],
        ));
    }

    public function testMatchRejectsMissingAttributes(): void
    {
        $engine = new CatalogCollectionRuleEngineService();

        self::assertFalse($engine->match(
            ['locale' => 'en'],
            ['slug' => 'garden'],
        ));
    }

    public function testMatchSupportsAllowedValueLists(): void
    {
        $engine = new CatalogCollectionRuleEngineService();

        self::assertTrue($engine->match(
            ['locale' => 'en', 'channel' => 'storefront'],
            ['locale' => ['de', 'en']],
        ));
        self::assertFalse($engine->match(
            ['locale' => 'fr', 'channel' => 'storefront'],
            ['locale' => ['de', 'en']],
        ));
    }

    public function testMatchSupportsArrayValuedProjectionFields(): void
    {
        $engine = new CatalogCollectionRuleEngineService();

        self::assertTrue($engine->match(
            ['tag_set' => ['winter', 'sale'], 'brand' => 'acme'],
            ['tag_set' => 'winter'],
        ));
        self::assertTrue($engine->match(
            ['tag_set' => ['winter', 'sale'], 'brand' => 'acme'],
            ['tag_set' => ['summer', 'sale']],
        ));
        self::assertFalse($engine->match(
            ['tag_set' => ['winter', 'sale'], 'brand' => 'acme'],
            ['tag_set' => ['summer', 'spring']],
        ));
    }
}
