<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Repository\Catalog;

use App\Cataloging\EntityInterface\Catalog\CatalogSyndicationDestinationEntityInterface;
use App\Cataloging\RepositoryInterface\Catalog\CatalogSyndicationDestinationRepositoryInterface;

/**
 * Provides repository services for category syndication destination repository.
 */
final class CatalogSyndicationDestinationRepository implements CatalogSyndicationDestinationRepositoryInterface
{
    /**
     * @var array<string,CatalogSyndicationDestinationEntityInterface>
     */
    private array $items = [];

    /**
     * Handles the save workflow.
     */
    public function save(CatalogSyndicationDestinationEntityInterface $destination): void
    {
        $this->items[$destination->destinationId()] = $destination;
    }

    /**
     * Finds the requested record in the underlying store.
     */
    public function find(string $destinationId): ?CatalogSyndicationDestinationEntityInterface
    {
        return $this->items[trim($destinationId)] ?? null;
    }

    /**
     * Handles the enabled destinations workflow.
     */
    public function enabledDestinations(): array
    {
        return array_values(array_filter(
            $this->items,
            static fn (CatalogSyndicationDestinationEntityInterface $destination): bool => $destination->enabled(),
        ));
    }
}
