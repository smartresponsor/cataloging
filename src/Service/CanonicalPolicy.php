<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the canonical policy application service.
 */
final class CanonicalPolicy
{
    /**
     * Handles the url workflow.
     */
    public function url(string $host, string $locale, string $slug): string
    {
        $l = strtolower($locale ?: 'en');
        $s = trim($slug, '/');

        return rtrim($host, '/').'/'.$l.'/'.$s;
    }
}
