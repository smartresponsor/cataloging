<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\RepositoryInterface;

use App\Cataloging\EntityInterface\CatalogSyndicationDeliveryRecordInterface;

/**
 * Defines the contract for category syndication delivery record repository.
 */
interface CatalogSyndicationDeliveryRecordRepositoryInterface
{
    /**
     * Handles the save workflow.
     */
    public function save(CatalogSyndicationDeliveryRecordInterface $record): void;

    /**
     * Finds the requested record in the underlying store.
     */
    public function find(string $deliveryId): ?CatalogSyndicationDeliveryRecordInterface;

    /**
     * @return list<CatalogSyndicationDeliveryRecordInterface>
     */
    public function recordsForPackage(string $packageId): array;

    /**
     * @return list<CatalogSyndicationDeliveryRecordInterface>
     */
    public function failedRecords(): array;
}
if (!class_exists(__NAMESPACE__.'\\SyndicationDeliveryRecordRepositoryInterface', false)) {
    class_alias(CatalogSyndicationDeliveryRecordRepositoryInterface::class, __NAMESPACE__.'\\SyndicationDeliveryRecordRepositoryInterface');
}
