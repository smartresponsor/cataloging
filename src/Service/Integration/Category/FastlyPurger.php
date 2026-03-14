<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Owner: Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
*/

namespace App\Service\Integration\Category;

final class FastlyPurger
{
    public function headerForKeys(array $keys): array
    {
        return ['Fastly-Soft-Purge-Set' => implode(' ', array_values(array_unique($keys)))];
    }
}
