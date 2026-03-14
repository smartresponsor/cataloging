<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service\Seo\Category;

final class NoindexPolicy
{
    public function shouldNoindex(bool $virtual): bool
    {
        return $virtual;
    }
}
