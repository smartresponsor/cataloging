<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\EntityInterface\CategorySyndicationDeliveryRecordInterface;
use App\PolicyInterface\CategorySyndicationHistoryPolicyInterface;

final class CategorySyndicationHistoryPolicy implements CategorySyndicationHistoryPolicyInterface
{
    public function assertDestinationId(string $destinationId): void
    {
        if ('' === trim($destinationId)) {
            throw new \InvalidArgumentException('Destination id must not be empty.');
        }
    }

    public function recordsForDestination(string $destinationId, array $records): array
    {
        $normalized = trim($destinationId);

        return array_values(array_filter(
            $records,
            static fn (CategorySyndicationDeliveryRecordInterface $record): bool => $record->destinationId() === $normalized,
        ));
    }
}
