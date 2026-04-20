<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 * Owner: Marketing America Corp
 */

namespace App\Cataloging\Tests\Category;

use App\Cataloging\Entity\CategoryMediaBinding;
use App\Cataloging\Entity\CategorySyndicationDestination;
use App\Cataloging\Policy\CategoryDestinationMediaFallbackPolicy;
use App\Cataloging\Policy\CategoryDestinationMediaReadinessPolicy;
use App\Cataloging\Policy\CategoryMediaApplicabilityPolicy;
use App\Cataloging\Policy\CategorySyndicationFallbackAwarePackageGatePolicy;
use App\Cataloging\Policy\CategorySyndicationMappingPolicy;
use App\Cataloging\Repository\CategoryMediaBindingRepository;
use App\Cataloging\Repository\CategorySyndicationDestinationRepository;
use App\Cataloging\Service\CatalogDestinationMediaFallbackService;
use App\Cataloging\Service\CatalogDestinationMediaReadinessService;
use App\Cataloging\Service\CatalogMediaApplicabilityService;
use App\Cataloging\Service\CatalogSyndicationFallbackAwarePackageGateService;
use App\Cataloging\Service\CatalogSyndicationMappingService;
use App\Cataloging\ValueObject\CatalogAuditContext;
use App\Cataloging\ValueObject\CategoryMediaRole;
use App\Cataloging\ValueObject\CategorySyndicationDestinationConfiguration;
use App\Cataloging\ValueObject\CategorySyndicationDestinationDefinition;
use App\Cataloging\ValueObject\CategorySyndicationPackageBuildRequest;
use App\Cataloging\ValueObject\CategorySyndicationPackageContext;
use PHPUnit\Framework\TestCase;

final class CatalogSyndicationFallbackAwarePackageGateServiceTest extends TestCase
{
    public function testDistinguishesStrictAndFallbackPublishability(): void
    {
        $destinationRepository = new CategorySyndicationDestinationRepository();
        $destinationRepository->save(CategorySyndicationDestination::register(
            new CategorySyndicationDestinationDefinition(
                'dest-1',
                'Search Export',
                'search',
                'export',
            ),
            new CategorySyndicationDestinationConfiguration(
                true,
                [
                    'channel' => 'web',
                    'locale' => 'en_US',
                    'requiredMediaRoles' => '["banner"]',
                ],
            ),
            'operator-1',
        ));

        $bindingRepository = new CategoryMediaBindingRepository();
        $bindingRepository->save(new CategoryMediaBinding(
            'bind-1',
            'cat-1',
            'asset-1',
            CategoryMediaRole::banner(),
            [],
            ['en_US'],
            true,
            true,
            [],
            'operator-1',
            new \DateTimeImmutable('now'),
        ));

        $mappingService = new CatalogSyndicationMappingService(new CategorySyndicationMappingPolicy());
        $readinessService = new CatalogDestinationMediaReadinessService(
            $destinationRepository,
            new CatalogMediaApplicabilityService($bindingRepository, new CategoryMediaApplicabilityPolicy()),
            new CategoryDestinationMediaReadinessPolicy(),
        );
        $fallbackService = new CatalogDestinationMediaFallbackService($destinationRepository, $bindingRepository, new CategoryDestinationMediaFallbackPolicy());
        $service = new CatalogSyndicationFallbackAwarePackageGateService(
            $mappingService,
            $readinessService,
            $fallbackService,
            new CategorySyndicationFallbackAwarePackageGatePolicy(),
        );

        $event = $service->buildGatedPublishPackage(
            new CategorySyndicationPackageBuildRequest(
                new CategorySyndicationPackageContext('pkg-1', 'dest-1', 'cat-1', 'v1', 'per_locale'),
                ['title' => 'Category One'],
                ['title' => 'title'],
                ['title'],
                new CatalogAuditContext('actor-1', 'test'),
            ),
        );

        /** @var array{strictPublishable:bool,fallbackPublishable:bool,warnings:list<string>,fallbackMatchedBindingIds:list<string>} $payload */
        $payload = $event->payload();
        self::assertFalse($payload['strictPublishable']);
        self::assertTrue($payload['fallbackPublishable']);
        self::assertContains('package_publishable_via_fallback_only', $payload['warnings']);
        self::assertSame(['bind-1'], $payload['fallbackMatchedBindingIds']);
    }
}
