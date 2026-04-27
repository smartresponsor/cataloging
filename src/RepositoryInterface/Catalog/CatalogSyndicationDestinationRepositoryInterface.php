<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\RepositoryInterface\Catalog;

use App\Cataloging\EntityInterface\Catalog\CatalogSyndicationDestinationEntityInterface;

/**
 * Defines the contract for category syndication destination repository.
 */
interface CatalogSyndicationDestinationRepositoryInterface
{
    /**
     * Handles the save workflow.
     */
    public function save(CatalogSyndicationDestinationEntityInterface $destination): void;

    /**
     * Finds the requested record in the underlying store.
     */
    public function find(string $destinationId): ?CatalogSyndicationDestinationEntityInterface;

    /**
     * @return list<CatalogSyndicationDestinationEntityInterface>
     */
    public function enabledDestinations(): array;
}
