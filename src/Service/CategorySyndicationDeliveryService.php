<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service;

use App\Entity\CategorySyndicationDeliveryRecord;
use App\Event\CategorySyndicationDeliveryRecorded;
use App\EventInterface\CategorySyndicationDeliveryRecordedInterface;
use App\PolicyInterface\CategorySyndicationDeliveryPolicyInterface;
use App\RepositoryInterface\CategorySyndicationDeliveryRecordRepositoryInterface;
use App\ServiceInterface\CategorySyndicationDeliveryServiceInterface;
use App\ValueObject\CategorySyndicationDeliveryStatus;

final class CategorySyndicationDeliveryService implements CategorySyndicationDeliveryServiceInterface
{
    public function __construct(
        private readonly CategorySyndicationDeliveryPolicyInterface $policy,
        private readonly CategorySyndicationDeliveryRecordRepositoryInterface $repository,
    ) {
    }

    public function recordDelivery(
        string $deliveryId,
        string $packageId,
        string $destinationId,
        string $categoryId,
        string $status,
        int $attempt,
        ?int $responseCode,
        string $responseMessage,
        string $actorId,
        string $reason,
    ): CategorySyndicationDeliveryRecordedInterface {
        $this->policy->assertStatus($status);
        $this->policy->assertAttempt($attempt);

        $normalizedStatus = new CategorySyndicationDeliveryStatus(trim($status));
        $normalizedResponseMessage = $this->policy->normalizeResponseMessage($responseMessage);
        $deliveredAt = 'delivered' === $normalizedStatus->status() ? new \DateTimeImmutable('now') : null;

        $record = new CategorySyndicationDeliveryRecord(
            trim($deliveryId),
            trim($packageId),
            trim($destinationId),
            trim($categoryId),
            $normalizedStatus,
            $attempt,
            $responseCode,
            $normalizedResponseMessage,
            $deliveredAt,
        );

        $this->repository->save($record);

        return new CategorySyndicationDeliveryRecorded(
            [
                'deliveryId' => $record->deliveryId(),
                'packageId' => $record->packageId(),
                'destinationId' => $record->destinationId(),
                'categoryId' => $record->categoryId(),
                'status' => $record->status()->status(),
                'attempt' => $record->attempt(),
                'responseCode' => $record->responseCode(),
                'responseMessage' => $record->responseMessage(),
                'deliveredAt' => $record->deliveredAt()?->format(DATE_ATOM),
                'retryable' => 'failed' === $record->status()->status(),
                'actorId' => trim($actorId),
                'reason' => trim($reason),
            ],
            new \DateTimeImmutable('now'),
        );
    }
}
