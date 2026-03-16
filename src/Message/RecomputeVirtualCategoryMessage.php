<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Message;

final class RecomputeVirtualCategoryMessage
{
    public function __construct(public readonly string $virtualCategoryId)
    {
    }
}
