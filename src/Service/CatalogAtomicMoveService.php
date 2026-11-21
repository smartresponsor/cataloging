<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ServiceInterface\CatalogAtomicMoveServiceInterface;

/**
 * Provides the atomic move application service.
 */
final class CatalogAtomicMoveService implements CatalogAtomicMoveServiceInterface
{
    /**
     * Handles the move workflow.
     */
    public function move(string $nodeId, ?string $newParentId): void
    {
    }

    /**
     * Handles the swap workflow.
     */
    public function swap(string $aId, string $bId): void
    {
    }
}
