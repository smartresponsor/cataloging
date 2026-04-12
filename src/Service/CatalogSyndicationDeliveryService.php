<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Entity\CategorySyndicationDeliveryRecord;
use App\Event\CategorySyndicationDeliveryRecorded;
use App\EventInterface\CategorySyndicationDeliveryRecordedInterface;
use App\PolicyInterface\CategorySyndicationDeliveryPolicyInterface;
use App\RepositoryInterface\CategorySyndicationDeliveryRecordRepositoryInterface;
use App\ServiceInterface\CatalogSyndicationDeliveryServiceInterface;
use App\ValueObject\CategorySyndicationDeliveryRecordRequest;
use App\ValueObject\CategorySyndicationDeliveryStatus;

/**
 * Provides the catalog syndication delivery service application service.
 */
final readonly class CatalogSyndicationDeliveryService implements CatalogSyndicationDeliveryServiceInterface
{
    /**
     * Initializes the catalog syndication delivery service service collaborators.
     */
    public function __construct(
        private CategorySyndicationDeliveryPolicyInterface $policy,
        private CategorySyndicationDeliveryRecordRepositoryInterface $repository,
    ) {
    }

    /**
     * Handles the record delivery workflow.
     */
    public function recordDelivery(
        CategorySyndicationDeliveryRecordRequest $request,
    ): CategorySyndicationDeliveryRecordedInterface {
        $context = $request->context();
        $attempt = $request->attempt();
        $audit = $request->auditContext();
        $this->policy->assertStatus($context->status());
        $this->policy->assertAttempt($attempt->attempt());

        $normalizedStatus = new CategorySyndicationDeliveryStatus(trim($context->status()));
        $normalizedResponseMessage = $this->policy->normalizeResponseMessage($attempt->responseMessage());
        $deliveredAt = 'delivered' === $normalizedStatus->status() ? new \DateTimeImmutable('now') : null;

        $record = new CategorySyndicationDeliveryRecord(
            trim($context->deliveryId()),
            trim($context->packageId()),
            trim($context->destinationId()),
            trim($context->categoryId()),
            $normalizedStatus,
            $attempt->attempt(),
            $attempt->responseCode(),
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
                'actorId' => trim($audit->actorId()),
                'reason' => trim($audit->reason()),
            ],
            new \DateTimeImmutable('now'),
        );
    }
}
