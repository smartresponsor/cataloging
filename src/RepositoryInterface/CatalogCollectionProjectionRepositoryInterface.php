<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\RepositoryInterface;

/**
 * Defines the contract for catalog collection projection repository.
 */
interface CatalogCollectionProjectionRepositoryInterface
{
    /**
     * @return list<array<string, list<bool|float|int|string>|bool|float|int|string|null>>
     */
    public function list(): array;
}
