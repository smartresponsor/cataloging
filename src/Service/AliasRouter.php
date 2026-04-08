<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the alias router application service.
 */
final class AliasRouter
{
    /**
     * Resolves the requested result for the provided input.
     */
    public function resolve(string $alias): string
    {
        return match ($alias) {
            'electronics' => 'cat-100',
            default => $alias,
        };
    }
}
