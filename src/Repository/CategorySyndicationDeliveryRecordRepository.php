<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Repository;

use App\EntityInterface\CategorySyndicationDeliveryRecordInterface;
use App\RepositoryInterface\CategorySyndicationDeliveryRecordRepositoryInterface;

final class CategorySyndicationDeliveryRecordRepository implements CategorySyndicationDeliveryRecordRepositoryInterface
{
    /**
     * @var array<string,CategorySyndicationDeliveryRecordInterface>
     */
    private array $items = [];

    public function save(CategorySyndicationDeliveryRecordInterface $record): void
    {
        $this->items[$record->deliveryId()] = $record;
    }

    public function find(string $deliveryId): ?CategorySyndicationDeliveryRecordInterface
    {
        return $this->items[trim($deliveryId)] ?? null;
    }

    public function recordsForPackage(string $packageId): array
    {
        return array_values(array_filter(
            $this->items,
            static fn (CategorySyndicationDeliveryRecordInterface $record): bool => $record->packageId() === trim($packageId),
        ));
    }

    public function failedRecords(): array
    {
        return array_values(array_filter(
            $this->items,
            static fn (CategorySyndicationDeliveryRecordInterface $record): bool => 'failed' === $record->status()->status(),
        ));
    }
}
