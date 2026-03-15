<?php

declare(strict_types=1);

namespace App\Service;

interface AtomicMoveInterface
{
    public function move(string $nodeId, ?string $newParentId): void;

    public function swap(string $aId, string $bId): void;
}
