<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\CategoryEntity;

use App\Cataloging\Service\Category\Rule\CategoryRuleAdminService;
use App\Cataloging\Service\Category\Rule\CategoryRuleEngine;
use App\Cataloging\ServiceInterface\Rule\RuleRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class CategoryRuleAdminServiceTest extends TestCase
{
    public function testPreviewCapsReturnedSampleSizeWithoutLosingMatchCount(): void
    {
        $service = new CategoryRuleAdminService(
            new class implements RuleRepositoryInterface {
                public function save(array $rule): string
                {
                    return 'rule-1';
                }

                public function find(string $id): array
                {
                    return [
                        'id' => $id,
                        'definition' => [
                            'condition' => [
                                'attr' => 'locale',
                                'op' => 'eq',
                                'value' => 'en',
                            ],
                        ],
                    ];
                }

                public function list(array $opt = []): array
                {
                    return [];
                }
            },
            new CategoryRuleEngine(),
        );

        $payload = [];
        for ($i = 0; $i < 60; ++$i) {
            $payload[] = ['locale' => 'en', 'position' => $i];
        }

        $preview = $service->preview('rule-1', $payload);

        self::assertSame(60, $preview['matched']);
        self::assertCount(50, $preview['sample']);
        self::assertSame(0, $preview['sample'][0]['position']);
        self::assertSame(49, $preview['sample'][49]['position']);
    }
}
