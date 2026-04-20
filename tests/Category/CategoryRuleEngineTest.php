<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\Category;

use App\Cataloging\Service\CollectionRuleEngine;
use PHPUnit\Framework\TestCase;

final class CategoryRuleEngineTest extends TestCase
{
    public function testMatchSupportsNullAsAnExplicitValue(): void
    {
        $engine = new CollectionRuleEngine();

        self::assertTrue($engine->match(
            ['slug' => null, 'locale' => 'en'],
            ['slug' => null],
        ));
    }

    public function testMatchRejectsMissingAttributes(): void
    {
        $engine = new CollectionRuleEngine();

        self::assertFalse($engine->match(
            ['locale' => 'en'],
            ['slug' => 'garden'],
        ));
    }

    public function testMatchSupportsAllowedValueLists(): void
    {
        $engine = new CollectionRuleEngine();

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
        $engine = new CollectionRuleEngine();

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
