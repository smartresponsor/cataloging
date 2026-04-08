<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\AtomicMoveInterface;
/**
 * Provides the atomic move application service.
 */
final class AtomicMove implements AtomicMoveInterface
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
