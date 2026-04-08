<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the canonical policy locale application service.
 */
final class CanonicalPolicyLocale
{
    /** @var array<string,string> */
    private array $hostByLocale;

    /** @param array<string,string> $hostByLocale */
    public function __construct(array $hostByLocale)
    {
        $this->hostByLocale = $hostByLocale;
    }
    /**
     * Handles the url workflow.
     */
    public function url(string $locale, string $slug): string
    {
        $host = $this->hostByLocale[strtolower($locale)] ?? ($this->hostByLocale['en'] ?? 'https://example.com');
        $s = trim($slug, '/');

        return rtrim($host, '/').'/'.strtolower($locale).'/'.$s;
    }
}
