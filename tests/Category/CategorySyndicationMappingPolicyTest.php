<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\CategoryEntity;

use App\Cataloging\Policy\CategorySyndicationMappingPolicy;
use PHPUnit\Framework\TestCase;

final class CategorySyndicationMappingPolicyTest extends TestCase
{
    public function testNormalizeFieldMapAndRequiredFields(): void
    {
        $policy = new CategorySyndicationMappingPolicy();

        self::assertSame(
            [
                'nameEntity' => 'title',
                'seoTitle' => 'seo_title',
                'slug' => 'handle',
            ],
            $policy->normalizeFieldMap([
                ' seoTitle ' => ' seo_title ',
                ' slug ' => ' handle ',
                ' nameEntity ' => ' title ',
            ]),
        );

        self::assertSame(
            ['handle', 'title'],
            $policy->normalizeRequiredFields([' title ', 'handle', 'title']),
        );
    }
}
