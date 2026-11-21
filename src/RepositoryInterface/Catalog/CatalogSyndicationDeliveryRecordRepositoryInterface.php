<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\RepositoryInterface\Catalog;

use App\Cataloging\EntityInterface\Catalog\CatalogSyndicationDeliveryRecordEntityInterface;

/**
 * Defines the contract for category syndication delivery record repository.
 */
interface CatalogSyndicationDeliveryRecordRepositoryInterface
{
    /**
     * Handles the save workflow.
     */
    public function save(CatalogSyndicationDeliveryRecordEntityInterface $record): void;

    /**
     * Finds the requested record in the underlying store.
     */
    public function find(string $deliveryId): ?CatalogSyndicationDeliveryRecordEntityInterface;

    /**
     * @return list<CatalogSyndicationDeliveryRecordEntityInterface>
     */
    public function recordsForPackage(string $packageId): array;

    /**
     * @return list<CatalogSyndicationDeliveryRecordEntityInterface>
     */
    public function failedRecords(): array;
}
