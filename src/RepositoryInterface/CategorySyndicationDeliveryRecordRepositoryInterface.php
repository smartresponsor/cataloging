<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\RepositoryInterface;

use App\EntityInterface\CategorySyndicationDeliveryRecordInterface;
/**
 * Defines the contract for category syndication delivery record repository.
 */
interface CategorySyndicationDeliveryRecordRepositoryInterface
{
    /**
     * Handles the save workflow.
     */
    public function save(CategorySyndicationDeliveryRecordInterface $record): void;
    /**
     * Finds the requested record in the underlying store.
     */
    public function find(string $deliveryId): ?CategorySyndicationDeliveryRecordInterface;

    /**
     * @return list<CategorySyndicationDeliveryRecordInterface>
     */
    public function recordsForPackage(string $packageId): array;

    /**
     * @return list<CategorySyndicationDeliveryRecordInterface>
     */
    public function failedRecords(): array;
}
