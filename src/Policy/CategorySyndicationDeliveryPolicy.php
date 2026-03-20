<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Policy;

use App\PolicyInterface\CategorySyndicationDeliveryPolicyInterface;

final class CategorySyndicationDeliveryPolicy implements CategorySyndicationDeliveryPolicyInterface
{
    private const STATUSES = ['pending', 'delivered', 'failed', 'retry_scheduled', 'skipped'];

    public function assertStatus(string $status): void
    {
        if (!in_array(trim($status), self::STATUSES, true)) {
            throw new \InvalidArgumentException('Unsupported syndication delivery status.');
        }
    }

    public function assertAttempt(int $attempt): void
    {
        if ($attempt < 1) {
            throw new \InvalidArgumentException('Delivery attempt must be greater than zero.');
        }
    }

    public function normalizeResponseMessage(string $responseMessage): string
    {
        return trim($responseMessage);
    }
}
