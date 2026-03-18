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
use App\Repository\CategoryMediaBindingRepository;
use App\Repository\CategorySyndicationDestinationRepository;
use App\Service\CategoryDestinationMediaReadinessService;
use App\Service\CategoryMediaApplicabilityService;
use App\Service\CategoryMediaGovernanceService;
use App\Service\CategorySyndicationDestinationService;
use PHPUnit\Framework\TestCase;

final class CategoryDestinationMediaReadinessServiceTest extends TestCase
{
    public function testEvaluateBuildsDestinationScopedMediaReadiness(): void
    {
        $bindingRepository = new CategoryMediaBindingRepository();
        $destinationRepository = new CategorySyndicationDestinationRepository();

        $governance = new CategoryMediaGovernanceService($bindingRepository, new CategoryMediaGovernancePolicy());
        $destinationService = new CategorySyndicationDestinationService(new CategorySyndicationDestinationPolicy(), $destinationRepository);
        $applicability = new CategoryMediaApplicabilityService($bindingRepository, new CategoryMediaApplicabilityPolicy());
        $service = new CategoryDestinationMediaReadinessService($destinationRepository, $applicability, new CategoryDestinationMediaReadinessPolicy());

        $governance->bind('bind-primary', 'category-1301', 'asset-primary', 'primary', ['storefront'], ['en_US'], true, true, [], 'operator-1', 'primary');
        $governance->bind('bind-hero', 'category-1301', 'asset-hero', 'hero', ['storefront'], ['en_US'], false, true, [], 'operator-1', 'hero');

        $destinationService->register(
            'destination-1301',
            'Storefront US',
            'storefront',
            'push',
            true,
            ['channel' => 'storefront', 'locale' => 'en_US', 'requiredMediaRoles' => ['primary', 'hero']],
            'operator-1',
            'register destination'
        );

        $event = $service->evaluate('destination-1301', 'category-1301', 'operator-9', 'destination media readiness');
        $payload = $event->payload();

        self::assertTrue($payload['publishable']);
        self::assertTrue($payload['checks']['destinationMediaPublishable']);
        self::assertSame('storefront', $payload['channel']);
        self::assertSame('en_US', $payload['locale']);
        self::assertSame(['bind-primary', 'bind-hero'], $payload['matchedBindingIds']);
    }
}
