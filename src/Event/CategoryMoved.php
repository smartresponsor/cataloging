/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 */
<?php
declare(strict_types=1);

namespace App\Event;

final class CategoryMoved
{
    public string $nodeId;
    public string $oldParentId;
    public string $newParentId;
    public string $treeId;
    public int $changedCount;

    public function __construct(string $nodeId, string $oldParentId, string $newParentId, string $treeId, int $changedCount)
    {
        $this->nodeId = $nodeId;
        $this->oldParentId = $oldParentId;
        $this->newParentId = $newParentId;
        $this->treeId = $treeId;
        $this->changedCount = $changedCount;
    }
}
