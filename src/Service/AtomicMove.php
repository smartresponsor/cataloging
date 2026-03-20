<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
Owner: Marketing America Corp
*/

namespace App\Service;

use App\ServiceInterface\AtomicMoveInterface;

final class AtomicMove implements AtomicMoveInterface
{
    public function move(string $nodeId, ?string $newParentId): void
    {
    }

    public function swap(string $aId, string $bId): void
    {
    }
}
