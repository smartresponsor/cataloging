<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\CategoryEntity;

use App\Cataloging\Policy\CatalogSyndicationDestinationPolicy;
use App\Cataloging\Policy\CategoryDestinationMediaReadinessPolicy;
use App\Cataloging\Policy\CategoryMediaApplicabilityPolicy;
use App\Cataloging\Policy\CategoryMediaGovernancePolicy;
use App\Cataloging\Repository\Catalog\CatalogCategoryMediaBindingRepository;
use App\Cataloging\Repository\Catalog\CatalogSyndicationDestinationRepository;
use App\Cataloging\Service\CatalogDestinationMediaReadinessService;
use App\Cataloging\Service\CatalogMediaApplicabilityService;
use App\Cataloging\Service\CatalogMediaGovernanceService;
use App\Cataloging\Service\CatalogSyndicationDestinationService;
use App\Cataloging\ValueObject\CatalogAuditContext;
use App\Cataloging\ValueObject\CatalogCategoryMediaBindingEntityScope;
use App\Cataloging\ValueObject\CatalogCategoryMediaBindingEntityState;
use App\Cataloging\ValueObject\CatalogSyndicationDestinationConfiguration;
use App\Cataloging\ValueObject\CatalogSyndicationDestinationDefinition;
use App\Cataloging\ValueObject\CatalogSyndicationDestinationRegisterRequest;
use App\Cataloging\ValueObject\CategoryDestinationMediaEvaluationRequest;
use App\Cataloging\ValueObject\CategoryMediaBindRequest;
use PHPUnit\Framework\TestCase;

final class CatalogDestinationMediaReadinessServiceTest extends TestCase
{
    public function testEvaluateBuildsDestinationScopedMediaReadiness(): void
    {
        $bindingRepository = new CatalogCategoryMediaBindingRepository();
        $destinationRepository = new CatalogSyndicationDestinationRepository();

        $governance = new CatalogMediaGovernanceService($bindingRepository, new CategoryMediaGovernancePolicy());
        $destinationService = new CatalogSyndicationDestinationService(new CatalogSyndicationDestinationPolicy(), $destinationRepository);
        $applicability = new CatalogMediaApplicabilityService($bindingRepository, new CategoryMediaApplicabilityPolicy());
        $service = new CatalogDestinationMediaReadinessService($destinationRepository, $applicability, new CategoryDestinationMediaReadinessPolicy());

        $governance->bind(
            new CategoryMediaBindRequest(
                new CatalogCategoryMediaBindingEntityScope(
                    'bind-primary',
                    'category-1301',
                    'asset-primary',
                    'primary',
                    ['storefront'],
                    ['en_US'],
                ),
                new CatalogCategoryMediaBindingEntityState(true, true, []),
                new CatalogAuditContext('operator-1', 'primary'),
            ),
        );
        $governance->bind(
            new CategoryMediaBindRequest(
                new CatalogCategoryMediaBindingEntityScope(
                    'bind-hero',
                    'category-1301',
                    'asset-hero',
                    'hero',
                    ['storefront'],
                    ['en_US'],
                ),
                new CatalogCategoryMediaBindingEntityState(false, true, []),
                new CatalogAuditContext('operator-1', 'hero'),
            ),
        );

        $destinationService->register(
            new CatalogSyndicationDestinationRegisterRequest(
                new CatalogSyndicationDestinationDefinition(
                    'destination-1301',
                    'Storefront US',
                    'storefront',
                    'push',
                ),
                new CatalogSyndicationDestinationConfiguration(
                    true,
                    [
                        'channel' => 'storefront',
                        'locale' => 'en_US',
                        'requiredMediaRoles' => ['primary', 'hero'],
                    ],
                ),
                new CatalogAuditContext('operator-1', 'register destination'),
            ),
        );

        $payload = $this->normalizePayload(
            $service->evaluate(
                new CategoryDestinationMediaEvaluationRequest(
                    'destination-1301',
                    'category-1301',
                    new CatalogAuditContext('operator-9', 'destination media readiness'),
                ),
            )->payload(),
        );

        self::assertTrue($payload['publishable']);
        self::assertTrue($payload['checks']['destinationMediaPublishable']);
        self::assertSame('storefront', $payload['channel']);
        self::assertSame('en_US', $payload['locale']);
        self::assertSame(['bind-primary', 'bind-hero'], $payload['matchedBindingIds']);
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array{publishable: bool, channel: string, locale: string, matchedBindingIds: list<string>, checks: array{destinationMediaPublishable: bool}}
     */
    private function normalizePayload(array $payload): array
    {
        $checks = is_array($payload['checks'] ?? null) ? $payload['checks'] : [];

        return [
            'publishable' => (bool) ($payload['publishable'] ?? false),
            'channel' => $this->scalarString($payload['channel'] ?? ''),
            'locale' => $this->scalarString($payload['locale'] ?? ''),
            'matchedBindingIds' => $this->stringList($payload['matchedBindingIds'] ?? []),
            'checks' => [
                'destinationMediaPublishable' => (bool) ($checks['destinationMediaPublishable'] ?? false),
            ],
        ];
    }

    /** @return list<string> */
    private function stringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_values(array_map(fn (mixed $item): string => $this->scalarString($item), $value));
    }

    private function scalarString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
