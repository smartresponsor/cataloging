<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Service;

final class CacheInvalidator
{
    public function invalidate(int|string $id): void
    {
        $key = 'category:'.$id;
        file_put_contents('report/category-cache-invalidated.log', $key."\n", flags: FILE_APPEND);
    }
}
