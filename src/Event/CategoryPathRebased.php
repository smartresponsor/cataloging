<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Event;

final class CategoryPathRebased
{
    public function __construct(
        public string $nodeId,
        public string $oldPath,
        public string $newPath,
        public int $countChildren,
    ) {
    }
}
