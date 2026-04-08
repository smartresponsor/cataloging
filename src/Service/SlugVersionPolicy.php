<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the slug version policy application service.
 */
final class SlugVersionPolicy
{
    /**
     * Handles the version key workflow.
     */
    public function versionKey(?string $slug, int $version): string
    {
        return trim((string) $slug).':v'.$version;
    }
}
