<?php

declare(strict_types=1);

namespace App\Tests\Category;

use App\Service\CategoryRuleEngine;
use PHPUnit\Framework\TestCase;

final class RuleCompositionTest extends TestCase
{
    public function testAndComposition(): void
    {
        $engine = new CategoryRuleEngine();

        $category = ['color' => 'red', 'depth' => 5];

        $rules = [
            'and' => [
                ['color' => ['eq' => 'red']],
                ['depth' => ['gt' => 3]],
            ],
        ];

        self::assertTrue($engine->match($category, $rules));
    }

    public function testOrComposition(): void
    {
        $engine = new CategoryRuleEngine();

        $category = ['color' => 'blue', 'depth' => 2];

        $rules = [
            'or' => [
                ['color' => ['eq' => 'red']],
                ['depth' => ['lt' => 3]],
            ],
        ];

        self::assertTrue($engine->match($category, $rules));
    }

    public function testNestedComposition(): void
    {
        $engine = new CategoryRuleEngine();

        $category = ['color' => 'blue', 'depth' => 5];

        $rules = [
            'and' => [
                [
                    'or' => [
                        ['color' => ['eq' => 'red']],
                        ['color' => ['eq' => 'blue']],
                    ],
                ],
                ['depth' => ['gt' => 3]],
            ],
        ];

        self::assertTrue($engine->match($category, $rules));
    }
}
