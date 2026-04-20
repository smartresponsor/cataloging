<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\Category;

use App\Cataloging\Policy\CategorySyndicationMappingPolicy;
use App\Cataloging\Service\CatalogSyndicationMappingService;
use App\Cataloging\ValueObject\CatalogAuditContext;
use App\Cataloging\ValueObject\CategorySyndicationPackageBuildRequest;
use App\Cataloging\ValueObject\CategorySyndicationPackageContext;
use PHPUnit\Framework\TestCase;

final class CatalogSyndicationMappingServiceTest extends TestCase
{
    public function testBuildPublishPackageReturnsMappedPayloadAndMissingFields(): void
    {
        $service = new CatalogSyndicationMappingService(new CategorySyndicationMappingPolicy());

        $event = $service->buildPublishPackage(
            new CategorySyndicationPackageBuildRequest(
                new CategorySyndicationPackageContext(
                    'pkg-100',
                    'dest-101',
                    'category-701',
                    'v1',
                    'per_locale',
                ),
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
                new CatalogAuditContext('operator-1', 'build destination package'),
            ),
        );

        /** @var array{payload:array{title:string,handle:string},publishable:bool,missingRequiredFields:list<string>} $payload */
        $payload = $event->payload();
        self::assertSame('Summer Shoes', $payload['payload']['title']);
        self::assertSame('summer-shoes', $payload['payload']['handle']);
        self::assertFalse($payload['publishable']);
        self::assertSame(['seo_title'], $payload['missingRequiredFields']);
    }
}
