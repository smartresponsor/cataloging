<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Tests\Category;

use App\Service\CategoryRuleEngine;
use PHPUnit\Framework\TestCase;

final class CategoryRuleEngineTest extends TestCase
{
    public function testMatchSupportsNullAsAnExplicitValue(): void
    {
        $engine = new CategoryRuleEngine();

        self::assertTrue($engine->match(
            ['slug' => null, 'locale' => 'en'],
            ['slug' => null],
        ));
    }

    public function testMatchRejectsMissingAttributes(): void
    {
        $engine = new CategoryRuleEngine();

        self::assertFalse($engine->match(
            ['locale' => 'en'],
            ['slug' => 'garden'],
        ));
    }

    public function testMatchSupportsAllowedValueLists(): void
    {
        $engine = new CategoryRuleEngine();

        self::assertTrue($engine->match(
            ['locale' => 'en', 'channel' => 'storefront'],
            ['locale' => ['de', 'en']],
        ));
        self::assertFalse($engine->match(
            ['locale' => 'fr', 'channel' => 'storefront'],
            ['locale' => ['de', 'en']],
        ));
    }
}
