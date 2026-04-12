<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Repository;

use App\EntityInterface\CategorySyndicationDeliveryRecordInterface;
use App\RepositoryInterface\CategorySyndicationDeliveryRecordRepositoryInterface;

/**
 * Provides repository services for category syndication delivery record repository.
 */
/** @noinspection PhpCSFixerValidationInspection */
final class CategorySyndicationDeliveryRecordRepository implements CategorySyndicationDeliveryRecordRepositoryInterface
{
    /**
     * @var array<string,CategorySyndicationDeliveryRecordInterface>
     */
    private array $items = [];

    /**
     * Handles the save workflow.
     */
    public function save(CategorySyndicationDeliveryRecordInterface $record): void
    {
        $this->items[$record->deliveryId()] = $record;
    }

    /**
     * Finds the requested record in the underlying store.
     */
    public function find(string $deliveryId): ?CategorySyndicationDeliveryRecordInterface
    {
        return $this->items[trim($deliveryId)] ?? null;
    }

    /**
     * Handles the records for package workflow.
     */
    public function recordsForPackage(string $packageId): array
    {
        return array_values(array_filter(
            $this->items,
            static fn (CategorySyndicationDeliveryRecordInterface $record): bool => $record->packageId() === trim($packageId),
        ));
    }

    /**
     * Handles the failed records workflow.
     */
    public function failedRecords(): array
    {
        return array_values(array_filter(
            $this->items,
            static fn (CategorySyndicationDeliveryRecordInterface $record): bool => 'failed' === $record->status()->status(),
        ));
    }
}
