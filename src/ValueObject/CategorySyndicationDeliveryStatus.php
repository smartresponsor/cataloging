<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationDeliveryStatusInterface;

/**
 * Represents the category syndication delivery status value.
 */
final readonly class CategorySyndicationDeliveryStatus implements CategorySyndicationDeliveryStatusInterface
{
    /**
     * Initializes the category syndication delivery status service collaborators.
     */
    public function __construct(
        private string $status,
    ) {
    }

    /**
     * Handles the status workflow.
     */
    public function status(): string
    {
        return $this->status;
    }
}
