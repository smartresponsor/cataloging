<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Category\Quota;

interface CategoryTokenBucketInterface
{
    public function take(int $n = 1): bool;
}
