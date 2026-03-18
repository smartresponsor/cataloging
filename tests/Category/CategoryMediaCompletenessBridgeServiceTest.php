<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category;

use App\Policy\CategoryCompletenessPolicy;
use App\Policy\CategoryMediaCoveragePolicy;
use App\Policy\CategoryMediaGovernancePolicy;
use App\Repository\CategoryMediaBindingRepository;
use App\Service\CategoryMediaCompletenessBridgeService;
use App\Service\CategoryMediaCoverageService;
use App\Service\CategoryMediaGovernanceService;
use PHPUnit\Framework\TestCase;

final class CategoryMediaCompletenessBridgeServiceTest extends TestCase
{
    public function testEvaluateUsesGovernedMediaBindingsForCompletenessChecks(): void
    {
        $repository = new CategoryMediaBindingRepository();
        $governance = new CategoryMediaGovernanceService($repository, new CategoryMediaGovernancePolicy());
        $coverage = new CategoryMediaCoverageService($repository, new CategoryMediaCoveragePolicy());
        $service = new CategoryMediaCompletenessBridgeService(new CategoryCompletenessPolicy(), $coverage);

        $governance->bind('bind-11', 'category-902', 'asset-primary', 'primary', ['storefront'], ['en_US'], true, true, [], 'operator-1', 'bind primary');

        $event = $service->evaluate('category-902', [
            'slug' => 'winter-coats',
            'seo' => ['title' => 'Winter Coats', 'description' => 'Shop winter coats'],
            'content' => ['body' => 'Cold weather outerwear'],
            'locale' => ['enabled' => ['en_US']],
            'media' => ['primaryAssetId' => ''],
            'aliases' => ['coats-winter'],
            'presentation' => ['bannerId' => '', 'htmlBlockId' => 'html-1'],
        ], 'operator-2', 'completeness with governed media');

        $payload = $event->payload();
        self::assertTrue($payload['complete']);
        self::assertTrue($payload['checks']['mediaReady']);
        self::assertTrue($payload['publicationChecks']['requiredMediaCoverageReady']);
        self::assertContains('bannerReady', $payload['warnings']);
    }
}
