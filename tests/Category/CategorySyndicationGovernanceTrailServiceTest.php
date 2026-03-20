<?php

declare(strict_types=1);

namespace App\Tests\Category;

use App\Policy\CategorySyndicationGovernanceTrailPolicy;
use App\Service\CategorySyndicationGovernanceTrailService;
use PHPUnit\Framework\TestCase;

final class CategorySyndicationGovernanceTrailServiceTest extends TestCase
{
    public function testRecordTrailIncludesPolicyDeliveryAndHistorySignals(): void
    {
        $service = new CategorySyndicationGovernanceTrailService(new CategorySyndicationGovernanceTrailPolicy());

        $event = $service->recordTrail(
            [
                'destinationId' => 'dst-1',
                'categoryId' => 'cat-1',
                'mediaPolicyMode' => 'prefer_exact_warn',
                'strictPublishable' => false,
                'fallbackPublishable' => true,
                'resolvedPublishable' => true,
                'fallbackUsed' => true,
                'warnings' => ['package_publishable_by_destination_media_policy_fallback'],
            ],
            [
                'destinationId' => 'dst-1',
                'categoryId' => 'cat-1',
                'status' => 'retry_scheduled',
                'retryable' => true,
            ],
            [
                'destinationId' => 'dst-1',
                'totalRecords' => 4,
                'deliveredCount' => 1,
                'failedCount' => 2,
                'pendingCount' => 0,
                'retryScheduledCount' => 1,
                'skippedCount' => 0,
            ],
            [
                'scheduledRetries' => 1,
            ],
            'actor-1',
            'test',
        );

        $payload = $event->payload();

        self::assertSame('prefer_exact_warn', $payload['mediaPolicyMode']);
        self::assertTrue($payload['resolvedPublishable']);
        self::assertTrue($payload['fallbackUsed']);
        self::assertTrue($payload['retryScheduled']);
        self::assertSame(2, $payload['historyCounts']['failedCount']);
        self::assertContains('governance_trail_fallback_used', $payload['warnings']);
        self::assertTrue($payload['checks']['governanceTrailHasFailures']);
    }
}
