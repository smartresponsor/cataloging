<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\EntityInterface\CategorySyndicationDeliveryRecordInterface;
use App\PolicyInterface\CategorySyndicationHistoryPolicyInterface;
/**
 * Provides the category syndication history policy implementation.
 */
final class CategorySyndicationHistoryPolicy implements CategorySyndicationHistoryPolicyInterface
{
    /**
     * Handles the assert destination id workflow.
     */
    public function assertDestinationId(string $destinationId): void
    {
        if ('' === trim($destinationId)) {
            throw new \InvalidArgumentException('Destination id must not be empty.');
        }
    }
    /**
     * Handles the records for destination workflow.
     */
    public function recordsForDestination(string $destinationId, array $records): array
    {
        $normalized = trim($destinationId);

        return array_values(array_filter(
            $records,
            static fn (CategorySyndicationDeliveryRecordInterface $record): bool =>
                $record->destinationId() === $normalized,
        ));
    }
}
