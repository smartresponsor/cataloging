<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\Category;

use App\Cataloging\Entity\Catalog\CatalogCategoryMediaBindingEntity;
use App\Cataloging\Entity\Catalog\CatalogSyndicationDestinationEntity;
use App\Cataloging\Policy\CategoryDestinationMediaFallbackPolicy;
use App\Cataloging\Policy\CategoryDestinationMediaPolicyPreferencePolicy;
use App\Cataloging\Policy\CategoryDestinationMediaReadinessPolicy;
use App\Cataloging\Repository\Catalog\CatalogCategoryMediaBindingRepository;
use App\Cataloging\Repository\Catalog\CatalogSyndicationDestinationRepository;
use App\Cataloging\Service\CatalogDestinationMediaFallbackService;
use App\Cataloging\Service\CatalogDestinationMediaPolicyPreferenceService;
use App\Cataloging\Service\CatalogDestinationMediaReadinessService;
use App\Cataloging\Service\CatalogMediaApplicabilityService;
use App\Cataloging\ValueObject\CatalogAuditContext;
use App\Cataloging\ValueObject\CatalogSyndicationDestinationConfiguration;
use App\Cataloging\ValueObject\CatalogSyndicationDestinationDefinition;
use App\Cataloging\ValueObject\CategoryDestinationMediaEvaluationRequest;
use App\Cataloging\ValueObject\CategoryMediaRole;
use PHPUnit\Framework\TestCase;

final class CatalogDestinationMediaPolicyPreferenceServiceTest extends TestCase
{
    public function testEvaluateResolvesPublishableViaFallbackWhenDestinationAllowsIt(): void
    {
        $destinationRepository = new CatalogSyndicationDestinationRepository();
        $destinationRepository->save(CatalogSyndicationDestinationEntity::register(
            new CatalogSyndicationDestinationDefinition(
                'destination-1901',
                'Storefront Feed',
                'storefront',
                'push',
            ),
            new CatalogSyndicationDestinationConfiguration(
                true,
                [
                    'channel' => 'storefront',
                    'locale' => 'en_US',
                    'requiredMediaRoles' => ['primary'],
                    'mediaPolicyMode' => 'allow_fallback',
                ],
            ),
            'operator-1',
        ));

        $bindingRepository = new CatalogCategoryMediaBindingRepository();
        $bindingRepository->save(new CatalogCategoryMediaBindingEntity(
            'binding-global-primary',
            'category-1901',
            'asset-global-primary',
            CategoryMediaRole::primary(),
            [],
            [],
            true,
            true,
            [],
            'operator-1',
            new \DateTimeImmutable('now'),
        ));

        $service = new CatalogDestinationMediaPolicyPreferenceService(
            $destinationRepository,
            new CatalogDestinationMediaReadinessService(
                $destinationRepository,
                new CatalogMediaApplicabilityService($bindingRepository, new \App\Cataloging\Policy\CategoryMediaApplicabilityPolicy()),
                new CategoryDestinationMediaReadinessPolicy(),
            ),
            new CatalogDestinationMediaFallbackService($destinationRepository, $bindingRepository, new CategoryDestinationMediaFallbackPolicy()),
            new CategoryDestinationMediaPolicyPreferencePolicy(),
        );

        $payload = $this->normalizePayload(
            $service->evaluate(
                new CategoryDestinationMediaEvaluationRequest(
                    'destination-1901',
                    'category-1901',
                    new CatalogAuditContext('operator-1', 'step08'),
                ),
            )->payload(),
        );
        self::assertSame('allow_fallback', $payload['mediaPolicyMode']);
        self::assertFalse($payload['strictPublishable']);
        self::assertTrue($payload['fallbackPublishable']);
        self::assertTrue($payload['resolvedPublishable']);
        self::assertTrue($payload['checks']['fallbackAcceptedByPolicy']);
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array{mediaPolicyMode: string, strictPublishable: bool, fallbackPublishable: bool, resolvedPublishable: bool, checks: array{fallbackAcceptedByPolicy: bool}}
     */
    private function normalizePayload(array $payload): array
    {
        $checks = is_array($payload['checks'] ?? null) ? $payload['checks'] : [];

        return [
            'mediaPolicyMode' => $this->scalarString($payload['mediaPolicyMode'] ?? ''),
            'strictPublishable' => (bool) ($payload['strictPublishable'] ?? false),
            'fallbackPublishable' => (bool) ($payload['fallbackPublishable'] ?? false),
            'resolvedPublishable' => (bool) ($payload['resolvedPublishable'] ?? false),
            'checks' => [
                'fallbackAcceptedByPolicy' => (bool) ($checks['fallbackAcceptedByPolicy'] ?? false),
            ],
        ];
    }

    private function scalarString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
