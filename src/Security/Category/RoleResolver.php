<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Security\Category;

final class RoleResolver
{
    public function hasRole(array $roles, string $needle): bool
    {
        return in_array($needle, $roles, true);
    }
}
