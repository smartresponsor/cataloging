<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 */

namespace App\Event;

final class CategoryMoved
{
    public function __construct(
        public string $nodeId,
        public string $oldParentId,
        public string $newParentId,
        public string $treeId,
        public int $changedCount,
    ) {
    }
}
