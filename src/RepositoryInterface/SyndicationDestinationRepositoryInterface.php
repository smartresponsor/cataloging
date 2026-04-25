<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\RepositoryInterface;

use App\Cataloging\EntityInterface\CatalogSyndicationDestinationEntityInterface;

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
if (!class_exists(__NAMESPACE__.'\\SyndicationDestinationRepositoryInterface', false)) {
    class_alias(CatalogSyndicationDestinationRepositoryInterface::class, __NAMESPACE__.'\\SyndicationDestinationRepositoryInterface');
}
