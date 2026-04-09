<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

/**
 * Provides the tree operation application service.
 */
final class TreeOperation
{
    /**
     * Handles the move workflow.
     */
    public function move(string $nodeId, ?string $newParentId): void
    {
        // Invariant guards. Integration with repositories should enforce ACID transaction.
        if ($nodeId === $newParentId) {
            throw new \InvalidArgumentException('Node cannot be parent of itself');
        }
        // Repository integration is expected at application layer.
    }

    /**
     * Handles the swap workflow.
     */
    public function swap(string $aId, string $bId): void
    {
        if ($aId === $bId) {
            throw new \InvalidArgumentException('Swap requires distinct nodes');
        }
    }

    /**
     * Handles the reparent workflow.
     */
    public function reparent(string $nodeId, ?string $newParentId): void
    {
        $this->move($nodeId, $newParentId);
    }
}
