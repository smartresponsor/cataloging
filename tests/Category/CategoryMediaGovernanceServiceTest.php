<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category;

use App\Policy\CategoryMediaGovernancePolicy;
use App\Repository\CategoryMediaBindingRepository;
use App\Service\CategoryMediaGovernanceService;
use PHPUnit\Framework\TestCase;

final class CategoryMediaGovernanceServiceTest extends TestCase
{
    public function testBindReturnsMediaBindingEventWithNormalizedPayload(): void
    {
        $repository = new CategoryMediaBindingRepository();
        $service = new CategoryMediaGovernanceService($repository, new CategoryMediaGovernancePolicy());

        $event = $service->bind(
            'binding-201',
            'category-701',
            'asset-501',
            'banner',
            ['storefront', 'storefront', 'mobile'],
            ['en_US', 'en_US', 'uk_UA'],
            true,
            true,
            ['format' => 'webp'],
            'operator-1',
            'bind category banner asset',
        );

        $payload = $event->payload();
        self::assertSame('binding-201', $payload['bindingId']);
        self::assertSame('banner', $payload['role']);
        self::assertSame(['storefront', 'mobile'], $payload['channels']);
        self::assertSame(['en_US', 'uk_UA'], $payload['locales']);
        self::assertTrue($payload['requiredForPublish']);
        self::assertSame('webp', $payload['metadata']['format']);
        self::assertCount(1, $repository->bindingsForCategory('category-701'));
        self::assertCount(1, $repository->history());
    }
}
