<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\RepositoryInterface\CatalogCollectionProjectionRepositoryInterface;
/**
 * Provides the catalog collection service application service.
 */
final class CatalogCollectionService
{
    /**
     * Initializes the catalog collection service service collaborators.
     */
    public function __construct(
        private readonly CatalogCollectionProjectionRepositoryInterface $repository,
        private readonly CollectionBuilder $builder,
    ) {
    }

    /**
     * @param array<string,array<int,bool|float|int|string>|bool|float|int|string> $rules
     *
     * @return list<array<string, list<bool|float|int|string>|bool|float|int|string|null>>
     */
    public function build(array $rules): array
    {
        return $this->builder->build($this->repository->list(), $rules);
    }
}
