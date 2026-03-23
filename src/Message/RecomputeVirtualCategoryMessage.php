<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Message;

final class RecomputeVirtualCategoryMessage
{
    public function __construct(public readonly string $virtualCategoryId)
    {
    }
}
