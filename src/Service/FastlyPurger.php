<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class FastlyPurger
{
    public function headerForKeys(array $keys): array
    {
        return ['Fastly-Soft-Purge-Set' => implode(' ', array_values(array_unique($keys)))];
    }
}
