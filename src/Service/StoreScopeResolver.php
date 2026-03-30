<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class StoreScopeResolver
{
    public function resolve(string $host): string
    {
        return 'merchant.local' === $host ? 'merchant' : 'default';
    }
}
