<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishченко / Marketing America Corp
Owner: Marketing America Corp
Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
*/

namespace App\Service;

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
        $host = $this->hostByLocale[strtolower($locale)] ?? ($this->hostByLocale['en'] ?? 'https://example.com');
        $s = trim($slug, '/');

        return rtrim($host, '/').'/'.strtolower($locale).'/'.$s;
    }
}
