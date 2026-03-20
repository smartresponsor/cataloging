<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Service;

final class StoreScopeResolver
{
    public function resolve(string $host): string
    {
        return 'merchant.local' === $host ? 'merchant' : 'default';
    }
}
