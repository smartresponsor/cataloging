<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 * Owner: Marketing America Corp
 */

namespace App\Tests\Category;

use App\Policy\CategorySyndicationCategoryGovernanceSummaryPolicy;
use App\Service\CatalogSyndicationCategoryGovernanceSummaryService;
use PHPUnit\Framework\TestCase;

final class CatalogSyndicationCategoryGovernanceSummaryServiceTest extends TestCase
{
    public function testBuildSummaryAggregatesTrailPayloadsAcrossDestinationsForCategory(): void
    {
        $service = new CatalogSyndicationCategoryGovernanceSummaryService(new CategorySyndicationCategoryGovernanceSummaryPolicy());

        $event = $service->buildSummary('cat-1', [
            [
                'destinationId' => 'dst-1',
                'deliveryStatus' => 'delivered',
                'mediaPolicyMode' => 'strict_exact',
                'resolvedPublishable' => true,
                'fallbackUsed' => false,
                'retryable' => false,
                'retryScheduled' => false,
                'warnings' => [],
                'checks' => [
                    'governanceTrailHasFailures' => false,
                    'governanceTrailHasDelivered' => true,
                ],
            ],
            [
                'destinationId' => 'dst-2',
                'deliveryStatus' => 'retry_scheduled',
                'mediaPolicyMode' => 'prefer_exact_warn',
                'resolvedPublishable' => true,
                'fallbackUsed' => true,
                'retryable' => true,
                'retryScheduled' => true,
                'warnings' => ['governance_trail_fallback_used'],
                'checks' => [
                    'governanceTrailHasFailures' => true,
                    'governanceTrailHasDelivered' => false,
                ],
            ],
        ], 'actor-1', 'test');

        $payload = $event->payload();

        self::assertSame('cat-1', $payload['categoryId']);
        self::assertSame(2, $payload['totalTrails']);
        self::assertSame(2, $payload['resolvedPublishableCount']);
        self::assertSame(1, $payload['fallbackUsedCount']);
        self::assertSame(1, $payload['retryScheduledCount']);
        self::assertSame(['dst-1', 'dst-2'], $payload['destinationIds']);
        self::assertSame(1, $payload['statusCounts']['delivered']);
        self::assertSame(1, $payload['statusCounts']['retry_scheduled']);
        self::assertSame(1, $payload['policyModeCounts']['prefer_exact_warn']);
        self::assertContains('governance_trail_fallback_used', $payload['warningCodes']);
        self::assertTrue($payload['checks']['categoryGovernanceSummaryHasDestinations']);
        self::assertTrue($payload['checks']['categoryGovernanceSummaryHasFailures']);
    }
}
