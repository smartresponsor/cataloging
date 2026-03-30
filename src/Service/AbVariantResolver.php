<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class AbVariantResolver
{
    /** @var array<string,bool> */
    private array $flags;

    /** @param array<string,bool> $flags */
    public function __construct(array $flags = [])
    {
        $this->flags = $flags;
    }

    public function variant(string $feature): string
    {
        $enabled = $this->flags[$feature] ?? false;
        $variant = $enabled ? 'v2' : 'v1';
        $logFile = 'report/category-ab-usage.json';
        /** @var list<array<string,string>> $log */
        $log = $this->readJsonList($logFile);
        $log[] = ['feature' => $feature, 'variant' => $variant, 'ts' => date(DATE_ATOM)];
        file_put_contents($logFile, json_encode($log, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        return $variant;
    }

    /** @return list<array<string,string>> */
    private function readJsonList(string $path): array
    {
        if (!is_file($path)) {
            return [];
        }
        $json = file_get_contents($path);
        if (!is_string($json) || '' === $json) {
            return [];
        }
        $decoded = json_decode($json, true);

        return is_array($decoded) ? array_values(array_filter($decoded, 'is_array')) : [];
    }
}
