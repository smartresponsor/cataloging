<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

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
