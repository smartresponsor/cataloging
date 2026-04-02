<?php

declare(strict_types=1);

namespace App\Tests\Category;

use App\Service\CategoryRuleEngine;
use App\Service\RuleNormalizer;
use PHPUnit\Framework\TestCase;

final class RuleDslTest extends TestCase
{
    public function testEqOperator(): void
    {
        $engine = new CategoryRuleEngine();

        $category = ['color' => 'red'];
        $rules = ['color' => ['eq' => 'red']];

        self::assertTrue($engine->match($category, $rules));
    }

    public function testInOperator(): void
    {
        $engine = new CategoryRuleEngine();

        $category = ['color' => 'red'];
        $rules = ['color' => ['in' => ['red', 'blue']]];

        self::assertTrue($engine->match($category, $rules));
    }

    public function testGtOperator(): void
    {
        $engine = new CategoryRuleEngine();

        $category = ['depth' => 5];
        $rules = ['depth' => ['gt' => 3]];

        self::assertTrue($engine->match($category, $rules));
    }

    public function testNormalizerSupportsOperatorMap(): void
    {
        $normalizer = new RuleNormalizer();

        $rules = [
            'color' => ['eq' => 'red'],
            'depth' => ['gt' => 3],
        ];

        $normalized = $normalizer->normalize($rules);

        self::assertArrayHasKey('color', $normalized);
        self::assertArrayHasKey('depth', $normalized);
    }
}
