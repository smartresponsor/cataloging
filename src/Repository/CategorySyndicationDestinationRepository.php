<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Repository;

use App\EntityInterface\CategorySyndicationDestinationInterface;
use App\RepositoryInterface\CategorySyndicationDestinationRepositoryInterface;

final class CategorySyndicationDestinationRepository implements CategorySyndicationDestinationRepositoryInterface
{
    /**
     * @var array<string,CategorySyndicationDestinationInterface>
     */
    private array $items = [];

    public function save(CategorySyndicationDestinationInterface $destination): void
    {
        $this->items[$destination->destinationId()] = $destination;
    }

    public function find(string $destinationId): ?CategorySyndicationDestinationInterface
    {
        return $this->items[trim($destinationId)] ?? null;
    }

    public function enabledDestinations(): array
    {
        return array_values(array_filter(
            $this->items,
            static fn (CategorySyndicationDestinationInterface $destination): bool => $destination->enabled(),
        ));
    }
}
