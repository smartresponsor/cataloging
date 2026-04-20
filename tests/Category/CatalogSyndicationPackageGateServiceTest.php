<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\Category;

use App\Cataloging\Policy\CategoryDestinationMediaReadinessPolicy;
use App\Cataloging\Policy\CategoryMediaApplicabilityPolicy;
use App\Cataloging\Policy\CategoryMediaGovernancePolicy;
use App\Cataloging\Policy\CategorySyndicationDestinationPolicy;
use App\Cataloging\Policy\CategorySyndicationMappingPolicy;
use App\Cataloging\Policy\CategorySyndicationPackageGatePolicy;
use App\Cataloging\Repository\CategoryMediaBindingRepository;
use App\Cataloging\Repository\CategorySyndicationDestinationRepository;
use App\Cataloging\Service\CatalogDestinationMediaReadinessService;
use App\Cataloging\Service\CatalogMediaApplicabilityService;
use App\Cataloging\Service\CatalogMediaGovernanceService;
use App\Cataloging\Service\CatalogSyndicationDestinationService;
use App\Cataloging\Service\CatalogSyndicationMappingService;
use App\Cataloging\Service\CatalogSyndicationPackageGateService;
use App\Cataloging\ValueObject\CatalogAuditContext;
use App\Cataloging\ValueObject\CategoryMediaBindingScope;
use App\Cataloging\ValueObject\CategoryMediaBindingState;
use App\Cataloging\ValueObject\CategoryMediaBindRequest;
use App\Cataloging\ValueObject\CategorySyndicationDestinationConfiguration;
use App\Cataloging\ValueObject\CategorySyndicationDestinationDefinition;
use App\Cataloging\ValueObject\CategorySyndicationDestinationRegisterRequest;
use App\Cataloging\ValueObject\CategorySyndicationPackageBuildRequest;
use App\Cataloging\ValueObject\CategorySyndicationPackageContext;
use PHPUnit\Framework\TestCase;

final class CatalogSyndicationPackageGateServiceTest extends TestCase
{
    public function testBuildGatedPublishPackageCombinesMappingAndDestinationMediaReadiness(): void
    {
        $bindingRepository = new CategoryMediaBindingRepository();
        $destinationRepository = new CategorySyndicationDestinationRepository();

        $governance = new CatalogMediaGovernanceService($bindingRepository, new CategoryMediaGovernancePolicy());
        $destinationService = new CatalogSyndicationDestinationService(new CategorySyndicationDestinationPolicy(), $destinationRepository);
        $applicabilityService = new CatalogMediaApplicabilityService($bindingRepository, new CategoryMediaApplicabilityPolicy());
        $destinationMediaReadiness = new CatalogDestinationMediaReadinessService($destinationRepository, $applicabilityService, new CategoryDestinationMediaReadinessPolicy());
        $mappingService = new CatalogSyndicationMappingService(new CategorySyndicationMappingPolicy());
        $service = new CatalogSyndicationPackageGateService($mappingService, $destinationMediaReadiness, new CategorySyndicationPackageGatePolicy());

        $governance->bind(new CategoryMediaBindRequest(new CategoryMediaBindingScope('bind-primary', 'category-1501', 'asset-primary', 'primary', ['storefront'], ['en_US']), new CategoryMediaBindingState(true, true, []), new CatalogAuditContext('operator-1', 'primary')));
        $governance->bind(new CategoryMediaBindRequest(new CategoryMediaBindingScope('bind-hero', 'category-1501', 'asset-hero', 'hero', ['storefront'], ['en_US']), new CategoryMediaBindingState(false, true, []), new CatalogAuditContext('operator-1', 'hero')));

        $destinationService->register(new CategorySyndicationDestinationRegisterRequest(
            new CategorySyndicationDestinationDefinition(
                'destination-1501',
                'Storefront US',
                'storefront',
                'push',
            ),
            new CategorySyndicationDestinationConfiguration(
                true,
                ['channel' => 'storefront', 'locale' => 'en_US', 'requiredMediaRoles' => '["primary","hero"]'],
            ),
            new CatalogAuditContext('operator-1', 'register destination'),
        ));

        $event = $service->buildGatedPublishPackage(
            new CategorySyndicationPackageBuildRequest(
                new CategorySyndicationPackageContext(
                    'pkg-1501',
                    'destination-1501',
                    'category-1501',
                    'v1',
                    'per_locale',
                ),
                ['name' => 'Summer Shoes', 'slug' => 'summer-shoes', 'seoTitle' => 'Summer Shoes'],
                ['name' => 'title', 'slug' => 'handle', 'seoTitle' => 'seo_title'],
                ['title', 'handle', 'seo_title'],
                new CatalogAuditContext('operator-7', 'build gated package'),
            ),
        );

        /** @var array{publishable:bool,checks:array{packageGatePublishable:bool},matchedBindingIds:list<string>,packageMissingRequiredFields:list<string>,mediaRequiredMissing:list<string>} $payload */
        $payload = $event->payload();
        self::assertTrue($payload['publishable']);
        self::assertTrue($payload['checks']['packageGatePublishable']);
        self::assertSame(['bind-hero', 'bind-primary'], $payload['matchedBindingIds']);
        self::assertSame([], $payload['packageMissingRequiredFields']);
        self::assertSame([], $payload['mediaRequiredMissing']);
    }
}
