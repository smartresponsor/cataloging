<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Service;

final class AliasRouter
{
    public function resolve(string $alias): string
    {
        return match ($alias) {
            'electronics' => 'cat-100',
            default => $alias,
        };
    }
}
