<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

/**
 * Provides the ab variant resolver application service.
 */
final class AbVariantResolver
{
    private const string USAGE_REPORT_FILE = 'report/category-ab-usage.json';

    /** @var array<string,bool> */
    private array $flags;

    /** @param array<string,bool> $flags */
    public function __construct(array $flags = [])
    {
        $this->flags = $flags;
    }

    /**
     * Handles the variant workflow.
     */
    public function variant(string $feature): string
    {
        $enabled = $this->flags[$feature] ?? false;
        $variant = $enabled ? 'v2' : 'v1';
        $this->storeUsage($feature, $variant);

        return $variant;
    }

    private function storeUsage(string $feature, string $variant): void
    {
        try {
            $logFile = self::USAGE_REPORT_FILE;
            $directory = dirname($logFile);
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            /** @var list<array<string,string>> $log */
            $log = $this->readJsonList($logFile);
            $log[] = ['feature' => $feature, 'variant' => $variant, 'ts' => date(DATE_ATOM)];
            $encoded = json_encode($log, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
            file_put_contents($logFile, $encoded);
        } catch (\Throwable) {
            // Best-effort observability only.
        }
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
