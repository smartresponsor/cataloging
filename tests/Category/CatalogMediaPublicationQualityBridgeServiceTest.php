<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Tests\Category;

use App\Policy\CategoryCompletenessPolicy;
use App\Policy\CategoryMediaCoveragePolicy;
use App\Policy\CategoryPublicationQualityPolicy;
use App\Repository\CategoryMediaBindingRepository;
use App\Service\CatalogMediaCompletenessBridgeService;
use App\Service\CatalogMediaCoverageService;
use App\Service\CatalogMediaPublicationQualityBridgeService;
use App\Service\CatalogPublicationQualityService;
use PHPUnit\Framework\TestCase;

final class CatalogMediaPublicationQualityBridgeServiceTest extends TestCase
{
    public function testEvaluatePromotesMissingRequiredMediaCoverageToHardBlocker(): void
    {
        $repository = new CategoryMediaBindingRepository();
        $coverage = new CatalogMediaCoverageService($repository, new CategoryMediaCoveragePolicy());
        $completenessBridge = new CatalogMediaCompletenessBridgeService(new CategoryCompletenessPolicy(), $coverage);
        $qualityBridge = new CatalogMediaPublicationQualityBridgeService($completenessBridge, new CatalogPublicationQualityService(new CategoryPublicationQualityPolicy()));

        $event = $qualityBridge->evaluate('category-903', [
            'slug' => 'clearance',
            'seo' => ['title' => 'Clearance', 'description' => 'Clearance offers'],
            'content' => ['body' => 'Discounted products'],
            'locale' => ['enabled' => ['en_US']],
            'media' => ['primaryAssetId' => 'asset-inline'],
            'aliases' => ['sale'],
            'presentation' => ['bannerId' => '', 'htmlBlockId' => 'html-9'],
        ], 'operator-3', 'quality with missing governed required media');

        $payload = $event->payload();
        self::assertFalse($payload['publishableQuality']);
        self::assertContains('requiredMediaCoverageReady', $payload['hardBlockers']);
        self::assertContains('bannerReady', $payload['advisoryWarnings']);
    }
}
