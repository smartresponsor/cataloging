<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ServiceInterface\AtomicMoveInterface;

final class AtomicMove implements AtomicMoveInterface
{
    public function move(string $nodeId, ?string $newParentId): void
    {
    }

    public function swap(string $aId, string $bId): void
    {
    }
}
