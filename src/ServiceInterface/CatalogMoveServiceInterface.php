<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\ValueObject\CatalogMoveRequest;

/**
 * Defines the contract for catalog move service.
 */
interface CatalogMoveServiceInterface
{
    /**
     * Perform a transactional rebase of the node path under the new parent.
     *
     * @return array{0:int,1:array<int,mixed>}
     */
    public function move(CatalogMoveRequest $request): array;
}
