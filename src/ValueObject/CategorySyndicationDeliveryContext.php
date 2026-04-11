<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries the identity and status surface for a syndication delivery record.
 */
final readonly class CategorySyndicationDeliveryContext
{
    /**
     * Initializes the category syndication delivery context value object.
     */
    public function __construct(
        private string $deliveryId,
        private string $packageId,
        private string $destinationId,
        private string $categoryId,
        private string $status,
    ) {
    }

    public function deliveryId(): string
    {
        return $this->deliveryId;
    }

    public function packageId(): string
    {
        return $this->packageId;
    }

    public function destinationId(): string
    {
        return $this->destinationId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function status(): string
    {
        return $this->status;
    }
}
