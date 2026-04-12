<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Event;

/**
 * Represents the category moved application event.
 */
final class CategoryMoved
{
    public string $nodeId;
    public string $oldParentId;
    public string $newParentId;
    public string $treeId;
    public int $changedCount;

    /**
     * Initializes the category moved service collaborators.
     */
    public function __construct(
        string $nodeId,
        string $oldParentId,
        string $newParentId,
        string $treeId,
        int $changedCount,
    ) {
        $this->nodeId = $nodeId;
        $this->oldParentId = $oldParentId;
        $this->newParentId = $newParentId;
        $this->treeId = $treeId;
        $this->changedCount = $changedCount;
    }
}
