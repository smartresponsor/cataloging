<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class TenantFilter
{
    /**
     * @param list<array<string,mixed>> $items
     *
     * @return list<array<string,mixed>>
     */
    public function filter(array $items, string $tenant): array
    {
        return array_values(array_filter(
            $items,
            static fn (array $i): bool => (($i['tenant'] ?? 'default') === $tenant)
        ));
    }
}
