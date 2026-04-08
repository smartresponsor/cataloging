<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the fastly purger application service.
 */
final class FastlyPurger
{
    /** @param list<string> $keys @return string */
    public function headerForKeys(array $keys): string
    {
        return implode(' ', array_values(array_unique($keys)));
    }
}
