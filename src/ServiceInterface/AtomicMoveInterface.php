<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

/**
 * Defines the contract for atomic move.
 */
interface AtomicMoveInterface
{
    /**
     * Handles the move workflow.
     */
    public function move(string $nodeId, ?string $newParentId): void;

    /**
     * Handles the swap workflow.
     */
    public function swap(string $aId, string $bId): void;
}
