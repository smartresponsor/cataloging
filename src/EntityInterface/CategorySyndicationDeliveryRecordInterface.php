<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\EntityInterface;

use App\Cataloging\ValueObjectInterface\CategorySyndicationDeliveryStatusInterface;

/**
 * Defines the contract for category syndication delivery record.
 */
interface CategorySyndicationDeliveryRecordInterface
{
    /**
     * Handles the delivery id workflow.
     */
    public function deliveryId(): string;

    /**
     * Handles the package id workflow.
     */
    public function packageId(): string;

    /**
     * Handles the destination id workflow.
     */
    public function destinationId(): string;

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string;

    /**
     * Handles the status workflow.
     */
    public function status(): CategorySyndicationDeliveryStatusInterface;

    /**
     * Handles the attempt workflow.
     */
    public function attempt(): int;

    /**
     * Handles the response code workflow.
     */
    public function responseCode(): ?int;

    /**
     * Handles the response message workflow.
     */
    public function responseMessage(): string;

    /**
     * Handles the delivered at workflow.
     */
    public function deliveredAt(): ?\DateTimeImmutable;
}
