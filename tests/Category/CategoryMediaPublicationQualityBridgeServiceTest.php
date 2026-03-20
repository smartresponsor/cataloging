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
use App\Service\CategoryMediaCompletenessBridgeService;
use App\Service\CategoryMediaCoverageService;
use App\Service\CategoryMediaPublicationQualityBridgeService;
use App\Service\CategoryPublicationQualityService;
use PHPUnit\Framework\TestCase;

final class CategoryMediaPublicationQualityBridgeServiceTest extends TestCase
{
    public function testEvaluatePromotesMissingRequiredMediaCoverageToHardBlocker(): void
    {
        $repository = new CategoryMediaBindingRepository();
        $coverage = new CategoryMediaCoverageService($repository, new CategoryMediaCoveragePolicy());
        $completenessBridge = new CategoryMediaCompletenessBridgeService(new CategoryCompletenessPolicy(), $coverage);
        $qualityBridge = new CategoryMediaPublicationQualityBridgeService($completenessBridge, new CategoryPublicationQualityService(new CategoryPublicationQualityPolicy()));

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
