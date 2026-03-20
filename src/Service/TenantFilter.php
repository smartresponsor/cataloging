<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Service;

final class TenantFilter
{
    public function filter(array $items, string $tenant): array
    {
        return array_values(array_filter($items, static fn ($i) => ($i['tenant'] ?? 'default') === $tenant));
    }
}
