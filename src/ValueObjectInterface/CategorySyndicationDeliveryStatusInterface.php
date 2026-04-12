<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObjectInterface;

/**
 * Defines the contract for category syndication delivery status.
 */
interface CategorySyndicationDeliveryStatusInterface
{
    /**
     * Handles the status workflow.
     */
    public function status(): string;
}
