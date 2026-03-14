<?php

declare(strict_types=1);

namespace App\ServiceInterface\Command\Category;

interface AtomicMoveInterface
{
    public function move(string $nodeId, ?string $newParentId): void;

    public function swap(string $aId, string $bId): void;
}
