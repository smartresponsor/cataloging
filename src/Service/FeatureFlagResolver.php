<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class FeatureFlagResolver
{
    /** @param array<string,bool> $flags */
    public function __construct(private readonly array $flags = [])
    {
    }

    public function isEnabled(string $flag): bool
    {
        return (bool) ($this->flags[$flag] ?? false);
    }
}
