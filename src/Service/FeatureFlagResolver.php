<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Service;

final class FeatureFlagResolver
{
    public function __construct(private readonly array $flags = [])
    {
    }

    public function isEnabled(string $flag): bool
    {
        return (bool) ($this->flags[$flag] ?? false);
    }
}
