<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\RepositoryInterface;

use App\Cataloging\EntityInterface\CategorySyndicationDestinationInterface;

/**
 * Defines the contract for category syndication destination repository.
 */
interface CategorySyndicationDestinationRepositoryInterface
{
    /**
     * Handles the save workflow.
     */
    public function save(CategorySyndicationDestinationInterface $destination): void;

    /**
     * Finds the requested record in the underlying store.
     */
    public function find(string $destinationId): ?CategorySyndicationDestinationInterface;

    /**
     * @return list<CategorySyndicationDestinationInterface>
     */
    public function enabledDestinations(): array;
}
