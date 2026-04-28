<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\Catalog\CatalogSyndicationDeliveryRecordEntity;
use App\Cataloging\Event\Catalog\CatalogSyndicationDeliveryRecordedEvent;
use App\Cataloging\EventInterface\Catalog\CatalogSyndicationDeliveryRecordedEventInterface;
use App\Cataloging\PolicyInterface\CategorySyndicationDeliveryPolicyInterface;
use App\Cataloging\RepositoryInterface\Catalog\CatalogSyndicationDeliveryRecordRepositoryInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationDeliveryServiceInterface;
use App\Cataloging\ValueObject\CatalogSyndicationDeliveryRecordRequest;
use App\Cataloging\ValueObject\CategorySyndicationDeliveryStatus;

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
        private CatalogSyndicationDeliveryRecordRepositoryInterface $repository,
    ) {
    }

    /**
     * Handles the record delivery workflow.
     */
    public function recordDelivery(
        CatalogSyndicationDeliveryRecordRequest $request,
    ): CatalogSyndicationDeliveryRecordedEventInterface {
        $context = $request->context();
        $attempt = $request->attempt();
        $audit = $request->auditContext();
        $this->policy->assertStatus($context->status());
        $this->policy->assertAttempt($attempt->attempt());

        $normalizedStatus = new CategorySyndicationDeliveryStatus(trim($context->status()));
        $normalizedResponseMessage = $this->policy->normalizeResponseMessage($attempt->responseMessage());
        $deliveredAt = 'delivered' === $normalizedStatus->status() ? new \DateTimeImmutable('now') : null;

        $record = new CatalogSyndicationDeliveryRecordEntity(
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

        return new CatalogSyndicationDeliveryRecordedEvent(
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
