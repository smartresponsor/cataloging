<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category;

use App\Policy\CategoryDestinationMediaReadinessPolicy;
use App\Policy\CategoryMediaApplicabilityPolicy;
use App\Policy\CategoryMediaGovernancePolicy;
use App\Policy\CategorySyndicationDestinationPolicy;
use App\Policy\CategorySyndicationMappingPolicy;
use App\Policy\CategorySyndicationPackageGatePolicy;
use App\Repository\CategoryMediaBindingRepository;
use App\Repository\CategorySyndicationDestinationRepository;
use App\Service\CategoryDestinationMediaReadinessService;
use App\Service\CategoryMediaApplicabilityService;
use App\Service\CategoryMediaGovernanceService;
use App\Service\CategorySyndicationDestinationService;
use App\Service\CategorySyndicationMappingService;
use App\Service\CategorySyndicationPackageGateService;
use PHPUnit\Framework\TestCase;

final class CategorySyndicationPackageGateServiceTest extends TestCase
{
    public function testBuildGatedPublishPackageCombinesMappingAndDestinationMediaReadiness(): void
    {
        $bindingRepository = new CategoryMediaBindingRepository();
        $destinationRepository = new CategorySyndicationDestinationRepository();

        $governance = new CategoryMediaGovernanceService($bindingRepository, new CategoryMediaGovernancePolicy());
        $destinationService = new CategorySyndicationDestinationService(new CategorySyndicationDestinationPolicy(), $destinationRepository);
        $applicabilityService = new CategoryMediaApplicabilityService($bindingRepository, new CategoryMediaApplicabilityPolicy());
        $destinationMediaReadiness = new CategoryDestinationMediaReadinessService($destinationRepository, $applicabilityService, new CategoryDestinationMediaReadinessPolicy());
        $mappingService = new CategorySyndicationMappingService(new CategorySyndicationMappingPolicy());
        $service = new CategorySyndicationPackageGateService($mappingService, $destinationMediaReadiness, new CategorySyndicationPackageGatePolicy());

        $governance->bind('bind-primary', 'category-1501', 'asset-primary', 'primary', ['storefront'], ['en_US'], true, true, [], 'operator-1', 'primary');
        $governance->bind('bind-hero', 'category-1501', 'asset-hero', 'hero', ['storefront'], ['en_US'], false, true, [], 'operator-1', 'hero');

        $destinationService->register(
            'destination-1501',
            'Storefront US',
            'storefront',
            'push',
            true,
            ['channel' => 'storefront', 'locale' => 'en_US', 'requiredMediaRoles' => ['primary', 'hero']],
            'operator-1',
            'register destination'
        );

        $event = $service->buildGatedPublishPackage(
            'pkg-1501',
            'destination-1501',
            'category-1501',
            'v1',
            'per_locale',
            ['name' => 'Summer Shoes', 'slug' => 'summer-shoes', 'seoTitle' => 'Summer Shoes'],
            ['name' => 'title', 'slug' => 'handle', 'seoTitle' => 'seo_title'],
            ['title', 'handle', 'seo_title'],
            'operator-7',
            'build gated package',
        );

        $payload = $event->payload();
        self::assertTrue($payload['publishable']);
        self::assertTrue($payload['checks']['packageGatePublishable']);
        self::assertSame(['bind-hero', 'bind-primary'], $payload['matchedBindingIds']);
        self::assertSame([], $payload['packageMissingRequiredFields']);
        self::assertSame([], $payload['mediaRequiredMissing']);
    }
}
