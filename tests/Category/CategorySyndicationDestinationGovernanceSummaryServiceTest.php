<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 * Owner: Marketing America Corp
 */

namespace App\Tests\Category;

use App\Policy\CategorySyndicationDestinationGovernanceSummaryPolicy;
use App\Service\CategorySyndicationDestinationGovernanceSummaryService;
use PHPUnit\Framework\TestCase;

final class CategorySyndicationDestinationGovernanceSummaryServiceTest extends TestCase
{
    public function testBuildSummaryAggregatesDestinationGovernanceTrailPayloads(): void
    {
        $service = new CategorySyndicationDestinationGovernanceSummaryService(new CategorySyndicationDestinationGovernanceSummaryPolicy());

        $event = $service->buildSummary('dst-1', [
            [
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

        self::assertSame('dst-1', $payload['destinationId']);
        self::assertSame(2, $payload['totalTrails']);
        self::assertSame(2, $payload['resolvedPublishableCount']);
        self::assertSame(1, $payload['fallbackUsedCount']);
        self::assertSame(1, $payload['retryScheduledCount']);
        self::assertSame(1, $payload['statusCounts']['delivered']);
        self::assertSame(1, $payload['statusCounts']['retry_scheduled']);
        self::assertSame(1, $payload['policyModeCounts']['prefer_exact_warn']);
        self::assertContains('governance_trail_fallback_used', $payload['warningCodes']);
        self::assertTrue($payload['checks']['destinationGovernanceSummaryHasDelivered']);
        self::assertTrue($payload['checks']['destinationGovernanceSummaryHasFailures']);
    }
}
