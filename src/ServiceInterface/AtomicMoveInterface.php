<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

interface AtomicMoveInterface
{
    public function move(string $nodeId, ?string $newParentId): void;

    public function swap(string $aId, string $bId): void;
}
