<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

/**
 * Provides the cloudflare purger application service.
 */
final class CloudflarePurger
{
    /**
     * @param list<string> $keys
     *
     * @return array{files:list<never>,tags:list<string>}
     */
    public function payloadForKeys(array $keys): array
    {
        return ['files' => [], 'tags' => array_values(array_unique($keys))];
    }
}
