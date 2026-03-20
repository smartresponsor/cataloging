<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
Owner: Marketing America Corp
*/

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
