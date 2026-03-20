<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\RepositoryInterface;

use App\EntityInterface\CategorySyndicationDestinationInterface;

interface CategorySyndicationDestinationRepositoryInterface
{
    public function save(CategorySyndicationDestinationInterface $destination): void;

    public function find(string $destinationId): ?CategorySyndicationDestinationInterface;

    /**
     * @return list<CategorySyndicationDestinationInterface>
     */
    public function enabledDestinations(): array;
}
