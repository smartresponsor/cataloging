<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Service;

final class RegionResolver
{
    public function resolve(string $host): string
    {
        return str_contains($host, 'eu') ? 'eu' : 'us';
    }
}
