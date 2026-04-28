<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\EntityInterface\Catalog\CatalogSyndicationDeliveryRecordEntityInterface;
use App\Cataloging\Event\Catalog\CatalogCategorySyndicationRecoveryCandidatePreparedEvent;
use App\Cataloging\Event\Catalog\CatalogCategorySyndicationRetryScheduledEvent;
use App\Cataloging\EventInterface\Catalog\CatalogCategorySyndicationRecoveryCandidatePreparedEventInterface;
use App\Cataloging\EventInterface\Catalog\CatalogCategorySyndicationRetryScheduledEventInterface;
use App\Cataloging\PolicyInterface\CategorySyndicationRetryPolicyInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationRetryServiceInterface;
use App\Cataloging\ValueObject\CategorySyndicationRecoveryCandidate;
use App\Cataloging\ValueObject\CategorySyndicationRetryPlan;

/**
 * Provides the catalog syndication retry service application service.
 */
final readonly class CatalogSyndicationRetryService implements CatalogSyndicationRetryServiceInterface
{
    /**
     * Initializes the catalog syndication retry service service collaborators.
     */
    public function __construct(
        private CategorySyndicationRetryPolicyInterface $policy,
    ) {
    }

    /**
     * Handles the prepare recovery candidate workflow.
     *
     * @throws \InvalidArgumentException
     */
    public function prepareRecoveryCandidate(
        CatalogSyndicationDeliveryRecordEntityInterface $record,
        string $actorId,
        string $reason,
    ): CatalogCategorySyndicationRecoveryCandidatePreparedEventInterface {
        $status = $record->status()->status();
        $this->policy->assertFailedStatus($status);
        $retryable = $this->policy->isRetryable($record->responseCode());

        $candidate = new CategorySyndicationRecoveryCandidate(
            $record->deliveryId(),
            $record->packageId(),
            $record->destinationId(),
            $record->categoryId(),
            $record->attempt(),
            $record->responseCode(),
            $record->responseMessage(),
            $retryable,
        );

        return new CatalogCategorySyndicationRecoveryCandidatePreparedEvent([
            'deliveryId' => $candidate->deliveryId(),
            'packageId' => $candidate->packageId(),
            'destinationId' => $candidate->destinationId(),
            'categoryId' => $candidate->categoryId(),
            'attempt' => $candidate->attempt(),
            'responseCode' => $candidate->responseCode(),
            'responseMessage' => $candidate->responseMessage(),
            'retryable' => $candidate->retryable(),
            'actorId' => trim($actorId),
            'reason' => trim($reason),
        ], new \DateTimeImmutable('now'));
    }

    /**
     * Schedules the retry workflow for later processing.
     *
     * @param CatalogSyndicationDeliveryRecordEntityInterface $record
     * @param string                                          $actorId
     * @param string                                          $reason
     *
     * @return CatalogCategorySyndicationRetryScheduledEventInterface
     *
     * @throws \DateMalformedStringException
     */
    public function scheduleRetry(
        CatalogSyndicationDeliveryRecordEntityInterface $record,
        string $actorId,
        string $reason,
    ): CatalogCategorySyndicationRetryScheduledEventInterface {
        $status = $record->status()->status();
        $this->policy->assertFailedStatus($status);

        $retryable = $this->policy->isRetryable($record->responseCode());
        if (!$retryable) {
            throw new \InvalidArgumentException('Delivery is not retryable.');
        }

        $nextAttempt = $this->policy->nextAttempt($record->attempt());
        $delaySeconds = $this->policy->delaySecondsForAttempt($nextAttempt);
        $scheduledAt = new \DateTimeImmutable(sprintf('+%d seconds', $delaySeconds));

        $plan = new CategorySyndicationRetryPlan(
            $record->deliveryId(),
            $record->packageId(),
            $record->destinationId(),
            $record->categoryId(),
            $nextAttempt,
            $delaySeconds,
            $scheduledAt,
            true,
        );

        return new CatalogCategorySyndicationRetryScheduledEvent([
            'deliveryId' => $plan->deliveryId(),
            'packageId' => $plan->packageId(),
            'destinationId' => $plan->destinationId(),
            'categoryId' => $plan->categoryId(),
            'nextAttempt' => $plan->nextAttempt(),
            'delaySeconds' => $plan->delaySeconds(),
            'scheduledAt' => $plan->scheduledAt()->format(DATE_ATOM),
            'retryable' => $plan->retryable(),
            'actorId' => trim($actorId),
            'reason' => trim($reason),
        ], new \DateTimeImmutable('now'));
    }
}
