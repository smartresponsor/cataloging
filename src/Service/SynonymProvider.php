<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the synonym provider application service.
 */
final class SynonymProvider
{
    /** @var array<string,array<string,list<string>>> */
    private array $synonyms = [
        'en' => [
            'tv' => ['television', 'smart tv'],
            'phone' => ['smartphone', 'iphone'],
        ],
        'es' => [
            'ropa' => ['vestimenta'],
        ],
    ];

    /** @return list<string> */
    public function get(string $locale, string $term): array
    {
        return $this->synonyms[$locale][$term] ?? [];
    }
}
