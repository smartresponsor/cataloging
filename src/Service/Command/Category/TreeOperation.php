<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service\Command\Category;

final class TreeOperation
{
    public function move(string $nodeId, ?string $newParentId): void
    {
        // Invariant guards. Integration with repositories should enforce ACID transaction.
        if ($nodeId === $newParentId) {
            throw new \InvalidArgumentException('Node cannot be parent of itself');
        }
        // Repository integration is expected at application layer.
    }

    public function swap(string $aId, string $bId): void
    {
        if ($aId === $bId) {
            throw new \InvalidArgumentException('Swap requires distinct nodes');
        }
    }

    public function reparent(string $nodeId, ?string $newParentId): void
    {
        $this->move($nodeId, $newParentId);
    }
}
