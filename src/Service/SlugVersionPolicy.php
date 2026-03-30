<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class SlugVersionPolicy
{
    public function versionKey(?string $slug, int $version): string
    {
        return trim((string) $slug).':v'.$version;
    }
}
