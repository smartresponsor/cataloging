<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\RepositoryInterface\CatalogCollectionProjectionRepositoryInterface;

/**
 * Provides the catalog collection service application service.
 */
final readonly class CatalogCollectionService
{
    /**
     * Initializes the catalog collection service service collaborators.
     */
    public function __construct(
        private CatalogCollectionProjectionRepositoryInterface $repository,
        private CollectionBuilder $builder,
    ) {
    }

    /**
     * @param array<string,bool|float|int|list<bool|float|int|string>|string|null> $rules
     *
     * @return list<array<string, list<bool|float|int|string>|bool|float|int|string|null>>
     */
    public function build(array $rules): array
    {
        return $this->builder->build($this->repository->list(), $rules);
    }
}
