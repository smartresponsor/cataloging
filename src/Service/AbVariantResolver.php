<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service;

final class AbVariantResolver
{
    public function __construct(private readonly array $flags = [])
    {
    }

    public function variant(string $feature): string
    {
        $enabled = (bool) ($this->flags[$feature] ?? false);
        $variant = $enabled ? 'v2' : 'v1';
        $logFile = 'report/category-ab-usage.json';
        $log = is_file($logFile) ? json_decode(file_get_contents($logFile), true) : [];
        $log[] = ['feature' => $feature, 'variant' => $variant, 'ts' => date(DATE_ATOM)];
        file_put_contents($logFile, json_encode($log, JSON_PRETTY_PRINT));

        return $variant;
    }
}
