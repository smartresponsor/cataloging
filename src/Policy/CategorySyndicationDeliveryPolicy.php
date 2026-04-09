<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\PolicyInterface\CategorySyndicationDeliveryPolicyInterface;

/**
 * Provides the category syndication delivery policy implementation.
 */
final class CategorySyndicationDeliveryPolicy implements CategorySyndicationDeliveryPolicyInterface
{
    private const array STATUSES = ['pending', 'delivered', 'failed', 'retry_scheduled', 'skipped'];

    /**
     * Handles the assert status workflow.
     */
    public function assertStatus(string $status): void
    {
        if (!in_array(trim($status), self::STATUSES, true)) {
            throw new \InvalidArgumentException('Unsupported syndication delivery status.');
        }
    }

    /**
     * Handles the assert attempt workflow.
     */
    public function assertAttempt(int $attempt): void
    {
        if ($attempt < 1) {
            throw new \InvalidArgumentException('Delivery attempt must be greater than zero.');
        }
    }

    /**
     * Handles the normalize response message workflow.
     */
    public function normalizeResponseMessage(string $responseMessage): string
    {
        return trim($responseMessage);
    }
}
