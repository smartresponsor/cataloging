<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class CanonicalPolicy
{
    public function url(string $host, string $locale, string $slug): string
    {
        $l = strtolower($locale ?: 'en');
        $s = trim($slug, '/');

        return rtrim($host, '/').'/'.$l.'/'.$s;
    }
}
