<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Repository;

use App\Cataloging\EntityInterface\CatalogSyndicationDeliveryRecordInterface;
use App\Cataloging\RepositoryInterface\CatalogSyndicationDeliveryRecordRepositoryInterface;

/**
 * Provides repository services for category syndication delivery record repository.
 */
/** @noinspection PhpCSFixerValidationInspection */
final class CatalogSyndicationDeliveryRecordRepository implements CatalogSyndicationDeliveryRecordRepositoryInterface
{
    /**
     * @var array<string,CatalogSyndicationDeliveryRecordInterface>
     */
    private array $items = [];

    /**
     * Handles the save workflow.
     */
    public function save(CatalogSyndicationDeliveryRecordInterface $record): void
    {
        $this->items[$record->deliveryId()] = $record;
    }

    /**
     * Finds the requested record in the underlying store.
     */
    public function find(string $deliveryId): ?CatalogSyndicationDeliveryRecordInterface
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
            static fn (CatalogSyndicationDeliveryRecordInterface $record): bool => $record->packageId() === trim($packageId),
        ));
    }

    /**
     * Handles the failed records workflow.
     */
    public function failedRecords(): array
    {
        return array_values(array_filter(
            $this->items,
            static fn (CatalogSyndicationDeliveryRecordInterface $record): bool => 'failed' === $record->status()->status(),
        ));
    }
}
if (!class_exists(__NAMESPACE__.'\\SyndicationDeliveryRecordRepository', false)) {
    class_alias(CatalogSyndicationDeliveryRecordRepository::class, __NAMESPACE__.'\\SyndicationDeliveryRecordRepository');
}
