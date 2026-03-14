<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Owner: Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
*/

namespace App\Service\Seo\Category;

final class CanonicalPolicyLocale
{
    /** @var array<string,string> */
    private array $hostByLocale;

    public function __construct(array $hostByLocale)
    {
        $this->hostByLocale = $hostByLocale;
    }

    public function url(string $locale, string $slug): string
    {
        $localeCode = strtolower($locale);
        $host = $this->hostByLocale[$localeCode] ?? ($this->hostByLocale['en'] ?? 'https://example.com');
        $normalizedSlug = trim($slug, '/');

        return rtrim($host, '/').'/'.$localeCode.'/'.$normalizedSlug;
    }
}
