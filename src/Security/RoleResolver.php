<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Security;

final class RoleResolver
{
    /** @param list<string> $roles */
    public function hasRole(array $roles, string $needle): bool
    {
        return in_array($needle, $roles, true);
    }
}
