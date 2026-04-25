<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\CatalogSyndicationDestinationHistoryBuilt;
use App\Cataloging\Event\CategorySyndicationRecoveryAuditConsolidated;
use App\Cataloging\EventInterface\CatalogSyndicationDestinationHistoryBuiltInterface;
use App\Cataloging\EventInterface\CategorySyndicationRecoveryAuditConsolidatedInterface;
use App\Cataloging\PolicyInterface\CategorySyndicationHistoryPolicyInterface;
use App\Cataloging\PolicyInterface\CategorySyndicationRetryPolicyInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationHistoryServiceInterface;
use App\Cataloging\ValueObject\CatalogSyndicationDestinationHistory;
use App\Cataloging\ValueObject\CategorySyndicationHistoryRequest;
use App\Cataloging\ValueObject\CategorySyndicationRecoveryAuditSummary;

/**
 * Provides the catalog syndication history service application service.
 */
final readonly class CatalogSyndicationHistoryService implements CatalogSyndicationHistoryServiceInterface
{
    /**
     * Initializes the catalog syndication history service service collaborators.
     */
    public function __construct(
        private CategorySyndicationHistoryPolicyInterface $historyPolicy,
        private CategorySyndicationRetryPolicyInterface $retryPolicy,
    ) {
    }

    /**
     * Builds the destination history result for the current workflow.
     */
    public function buildDestinationHistory(
        CategorySyndicationHistoryRequest $request,
    ): CatalogSyndicationDestinationHistoryBuiltInterface {
        $this->historyPolicy->assertDestinationId($request->destinationId());
        $filtered = $this->historyPolicy->recordsForDestination($request->destinationId(), $request->records());

        $packageIds = [];
        $categoryIds = [];
        $delivered = 0;
        $failed = 0;
        $pending = 0;
        $retryScheduled = 0;
        $skipped = 0;
        $maxAttempt = 0;
        $latestDeliveredAt = null;

        foreach ($filtered as $record) {
            $packageIds[$record->packageId()] = true;
            $categoryIds[$record->categoryId()] = true;
            $maxAttempt = max($maxAttempt, $record->attempt());
            $status = $record->status()->status();

            match ($status) {
                'delivered' => $delivered++,
                'failed' => $failed++,
                'pending' => $pending++,
                'retry_scheduled' => $retryScheduled++,
                'skipped' => $skipped++,
                default => null,
            };

            if (
                null !== $record->deliveredAt()
                && (null === $latestDeliveredAt || $record->deliveredAt() > $latestDeliveredAt)
            ) {
                $latestDeliveredAt = $record->deliveredAt();
            }
        }

        $history = new CatalogSyndicationDestinationHistory(
            trim($request->destinationId()),
            array_keys($packageIds),
            array_keys($categoryIds),
            count($filtered),
            $delivered,
            $failed,
            $pending,
            $retryScheduled,
            $skipped,
            $maxAttempt,
            $latestDeliveredAt,
        );

        return new CatalogSyndicationDestinationHistoryBuilt([
            'destinationId' => $history->destinationId(),
            'packageIds' => $history->packageIds(),
            'categoryIds' => $history->categoryIds(),
            'totalRecords' => $history->totalRecords(),
            'deliveredCount' => $history->deliveredCount(),
            'failedCount' => $history->failedCount(),
            'pendingCount' => $history->pendingCount(),
            'retryScheduledCount' => $history->retryScheduledCount(),
            'skippedCount' => $history->skippedCount(),
            'maxAttempt' => $history->maxAttempt(),
            'latestDeliveredAt' => $history->latestDeliveredAt()?->format(DATE_ATOM),
            'actorId' => trim($request->actorId()),
            'reason' => trim($request->reason()),
        ], new \DateTimeImmutable('now'));
    }

    /**
     * Handles the consolidate recovery audit workflow.
     */
    public function consolidateRecoveryAudit(
        CategorySyndicationHistoryRequest $request,
    ): CategorySyndicationRecoveryAuditConsolidatedInterface {
        $this->historyPolicy->assertDestinationId($request->destinationId());
        $filtered = $this->historyPolicy->recordsForDestination($request->destinationId(), $request->records());

        $totalFailed = 0;
        $retryableFailed = 0;
        $nonRetryableFailed = 0;
        $scheduledRetries = 0;
        $deliveredAfterRetry = 0;
        $maxAttemptSeen = 0;
        $affectedCategoryIds = [];

        foreach ($filtered as $record) {
            $maxAttemptSeen = max($maxAttemptSeen, $record->attempt());
            $affectedCategoryIds[$record->categoryId()] = true;
            $status = $record->status()->status();

            if ('failed' === $status) {
                ++$totalFailed;
                if ($this->retryPolicy->isRetryable($record->responseCode())) {
                    ++$retryableFailed;
                } else {
                    ++$nonRetryableFailed;
                }
            }

            if ('retry_scheduled' === $status) {
                ++$scheduledRetries;
            }

            if ('delivered' === $status && $record->attempt() > 1) {
                ++$deliveredAfterRetry;
            }
        }

        $summary = new CategorySyndicationRecoveryAuditSummary(
            trim($request->destinationId()),
            $totalFailed,
            $retryableFailed,
            $nonRetryableFailed,
            $scheduledRetries,
            $deliveredAfterRetry,
            $maxAttemptSeen,
            array_keys($affectedCategoryIds),
        );

        return new CategorySyndicationRecoveryAuditConsolidated([
            'destinationId' => $summary->destinationId(),
            'totalFailed' => $summary->totalFailed(),
            'retryableFailed' => $summary->retryableFailed(),
            'nonRetryableFailed' => $summary->nonRetryableFailed(),
            'scheduledRetries' => $summary->scheduledRetries(),
            'deliveredAfterRetry' => $summary->deliveredAfterRetry(),
            'maxAttemptSeen' => $summary->maxAttemptSeen(),
            'affectedCategoryIds' => $summary->affectedCategoryIds(),
            'actorId' => trim($request->actorId()),
            'reason' => trim($request->reason()),
        ], new \DateTimeImmutable('now'));
    }
}
