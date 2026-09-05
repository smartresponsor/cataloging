<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\RepositoryInterface\Catalog;

/**
 * Defines the contract for catalog collection projection repository.
 */
interface CatalogCollectionProjectionRepositoryInterface
{
    /**
     * @return list<array<string, list<bool|float|int|string>|bool|float|int|string|null>>
     */
    public function list(): array;

    /** @return array{id:string,brand:?string,price:?float,stock:?int,tag_set?:list<bool|float|int|string>}|null */
    public function find(string $id): ?array;
}
