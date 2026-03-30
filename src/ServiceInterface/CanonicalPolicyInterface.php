<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

interface CanonicalPolicyInterface
{
    public function url(string $host, string $locale, string $slug): string;
}
