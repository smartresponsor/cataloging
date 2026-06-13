<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\CategoryEntity;

use App\Cataloging\Policy\CatalogSyndicationDestinationPolicy;
use App\Cataloging\Policy\CategoryDestinationMediaFallbackPolicy;
use App\Cataloging\Repository\Catalog\CatalogCategoryMediaBindingRepository;
use App\Cataloging\Repository\Catalog\CatalogSyndicationDestinationRepository;
use App\Cataloging\Service\CatalogDestinationMediaFallbackService;
use App\Cataloging\Service\CatalogSyndicationDestinationService;
use App\Cataloging\ValueObject\CatalogAuditContext;
use App\Cataloging\ValueObject\CatalogSyndicationDestinationConfiguration;
use App\Cataloging\ValueObject\CatalogSyndicationDestinationDefinition;
use App\Cataloging\ValueObject\CatalogSyndicationDestinationRegisterRequest;
use App\Cataloging\ValueObject\CategoryDestinationMediaEvaluationRequest;
use PHPUnit\Framework\TestCase;

final class CatalogDestinationMediaFallbackServiceTest extends TestCase
{
    public function testEvaluateBuildsFallbackAwareDestinationMediaReport(): void
    {
        $bindingRepository = new CatalogCategoryMediaBindingRepository();
        $destinationRepository = new CatalogSyndicationDestinationRepository();

        $destinationService = new CatalogSyndicationDestinationService(new CatalogSyndicationDestinationPolicy(), $destinationRepository);
        $service = new CatalogDestinationMediaFallbackService($destinationRepository, $bindingRepository, new CategoryDestinationMediaFallbackPolicy());

        $bindingRepository->save(new \App\Cataloging\Entity\Catalog\CatalogCategoryMediaBindingEntity(
            'bind-global-primary',
            'category-1802',
            'asset-primary',
            \App\Cataloging\ValueObject\CategoryMediaRole::primary(),
            [],
            [],
            true,
            true,
            [],
            'operator-1',
            new \DateTimeImmutable('now'),
        ));

        $destinationService->register(
            new CatalogSyndicationDestinationRegisterRequest(
                new CatalogSyndicationDestinationDefinition(
                    'destination-1802',
                    'Storefront CA French',
                    'storefront',
                    'push',
                ),
                new CatalogSyndicationDestinationConfiguration(
                    true,
                    ['channel' => 'storefront', 'locale' => 'fr_CA', 'requiredMediaRoles' => ['primary']],
                ),
                new CatalogAuditContext('operator-1', 'register destination'),
            ),
        );

        $payload = $this->normalizePayload(
            $service->evaluate(
                new CategoryDestinationMediaEvaluationRequest(
                    'destination-1802',
                    'category-1802',
                    new CatalogAuditContext('operator-9', 'evaluate fallback'),
                ),
            )->payload(),
        );
        self::assertFalse($payload['publishable']);
        self::assertTrue($payload['publishableWithFallback']);
        self::assertSame(['bind-global-primary'], $payload['fallbackMatchedBindingIds']);
        self::assertSame([], $payload['requiredMissing']);
        self::assertContains('sharedFallbackUsed', $payload['warnings']);
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array{publishable: bool, publishableWithFallback: bool, fallbackMatchedBindingIds: list<string>, requiredMissing: list<string>, warnings: list<string>}
     */
    private function normalizePayload(array $payload): array
    {
        return [
            'publishable' => (bool) ($payload['publishable'] ?? false),
            'publishableWithFallback' => (bool) ($payload['publishableWithFallback'] ?? false),
            'fallbackMatchedBindingIds' => $this->stringList($payload['fallbackMatchedBindingIds'] ?? []),
            'requiredMissing' => $this->stringList($payload['requiredMissing'] ?? []),
            'warnings' => $this->stringList($payload['warnings'] ?? []),
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
