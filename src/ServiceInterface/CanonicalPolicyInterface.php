<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 * Owner: Marketing America Corp
 */

namespace App\ServiceInterface;

interface CanonicalPolicyInterface
{
    public function url(string $host, string $locale, string $slug): string;
}
