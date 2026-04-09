<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

use App\EntityInterface\CategorySyndicationDeliveryRecordInterface;
use App\ValueObjectInterface\CategorySyndicationDeliveryStatusInterface;
/**
 * Represents the category syndication delivery record domain record.
 */
final class CategorySyndicationDeliveryRecord implements CategorySyndicationDeliveryRecordInterface
{
    /**
     * Initializes the category syndication delivery record service collaborators.
     */
    public function __construct(
        private readonly string $deliveryId,
        private readonly string $packageId,
        private readonly string $destinationId,
        private readonly string $categoryId,
        private readonly CategorySyndicationDeliveryStatusInterface $status,
        private readonly int $attempt,
        private readonly ?int $responseCode,
        private readonly string $responseMessage,
        private readonly ?\DateTimeImmutable $deliveredAt,
    ) {
    }
    /**
     * Handles the delivery id workflow.
     */
    public function deliveryId(): string
    {
        return $this->deliveryId;
    }
    /**
     * Handles the package id workflow.
     */
    public function packageId(): string
    {
        return $this->packageId;
    }
    /**
     * Handles the destination id workflow.
     */
    public function destinationId(): string
    {
        return $this->destinationId;
    }
    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string
    {
        return $this->categoryId;
    }
    /**
     * Handles the status workflow.
     */
    public function status(): CategorySyndicationDeliveryStatusInterface
    {
        return $this->status;
    }
    /**
     * Handles the attempt workflow.
     */
    public function attempt(): int
    {
        return $this->attempt;
    }
    /**
     * Handles the response code workflow.
     */
    public function responseCode(): ?int
    {
        return $this->responseCode;
    }
    /**
     * Handles the response message workflow.
     */
    public function responseMessage(): string
    {
        return $this->responseMessage;
    }
    /**
     * Handles the delivered at workflow.
     */
    public function deliveredAt(): ?\DateTimeImmutable
    {
        return $this->deliveredAt;
    }
}
