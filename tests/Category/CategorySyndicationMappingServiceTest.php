<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category;

use App\Policy\CategorySyndicationMappingPolicy;
use App\Service\CategorySyndicationMappingService;
use PHPUnit\Framework\TestCase;

final class CategorySyndicationMappingServiceTest extends TestCase
{
    public function testBuildPublishPackageReturnsMappedPayloadAndMissingFields(): void
    {
        $service = new CategorySyndicationMappingService(new CategorySyndicationMappingPolicy());

        $event = $service->buildPublishPackage(
            'pkg-100',
            'dest-101',
            'category-701',
            'v1',
            'per_locale',
            [
                'name' => 'Summer Shoes',
                'slug' => 'summer-shoes',
                'seoTitle' => '',
            ],
            [
                'name' => 'title',
                'slug' => 'handle',
                'seoTitle' => 'seo_title',
            ],
            ['title', 'handle', 'seo_title'],
            'operator-1',
            'build destination package',
        );

        $payload = $event->payload();
        self::assertTrue(isset($payload['payload']['title']));
        self::assertSame('Summer Shoes', $payload['payload']['title']);
        self::assertSame('summer-shoes', $payload['payload']['handle']);
        self::assertFalse($payload['publishable']);
        self::assertSame(['seo_title'], $payload['missingRequiredFields']);
    }
}
