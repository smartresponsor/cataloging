<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service\Category;

final class CanonicalPolicyLocale
{
    public function getCanonical(string $slug, string $locale): string
    {
        return sprintf('/%s/category/%s', $locale, $slug);
    }
}
