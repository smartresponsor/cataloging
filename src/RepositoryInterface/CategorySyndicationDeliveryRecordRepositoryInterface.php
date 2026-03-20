<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\RepositoryInterface;

use App\EntityInterface\CategorySyndicationDeliveryRecordInterface;

interface CategorySyndicationDeliveryRecordRepositoryInterface
{
    public function save(CategorySyndicationDeliveryRecordInterface $record): void;

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
