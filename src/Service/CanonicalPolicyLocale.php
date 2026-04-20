<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ValueObject\CategoryLocaleCanonicalUrlRequest;

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
    public function url(CategoryLocaleCanonicalUrlRequest $request): string
    {
        $locale = $request->locale();
        $host = $this->hostByLocale[$locale] ?? ($this->hostByLocale['en'] ?? 'https://example.com');

        return rtrim($host, '/').'/'.$locale.'/'.$request->slug();
    }
}
