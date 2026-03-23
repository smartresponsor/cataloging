<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Event;

final class CategoryPathRebased
{
    public string $nodeId;
    public string $oldPath;
    public string $newPath;
    public int $countChildren;

    public function __construct(string $nodeId, string $oldPath, string $newPath, int $countChildren)
    {
        $this->nodeId = $nodeId;
        $this->oldPath = $oldPath;
        $this->newPath = $newPath;
        $this->countChildren = $countChildren;
    }
}
