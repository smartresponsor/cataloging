<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the importer application service.
 */
final class CatalogImporterService
{
    /**
     * Handles the import csv workflow.
     *
     * @throws \RuntimeException
     */
    public function importCsv(string $path): int
    {
        $count = 0;
        $fh = fopen($path, 'r');
        if (false === $fh) {
            throw new \RuntimeException('Cannot open CSV');
        }
        /** @var list<string>|null $header */
        $header = null;
        while (($row = fgetcsv($fh)) !== false) {
            if (null === $header) {
                $header = array_map([$this, 'stringValue'], $row);
                continue;
            }
            /** @var array<string, bool|float|int|string|null> $item */
            $item = array_combine($header, $row);
            $this->upsert($item);
            ++$count;
        }
        fclose($fh);

        return $count;
    }

    /**
     * Handles the import json workflow.
     *
     * @throws \JsonException
     * @throws \RuntimeException
     */
    public function importJson(string $path): int
    {
        $raw = file_get_contents($path);
        if (false === $raw) {
            throw new \RuntimeException('Cannot read JSON');
        }
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            return 0;
        }

        $count = 0;
        foreach ($decoded as $item) {
            if (!is_array($item)) {
                continue;
            }
            /* @var array<string, scalar|null> $item */
            $this->upsert($item);
            ++$count;
        }

        return $count;
    }

    /** @param array<string, scalar|null> $item */
    private function upsert(array $item): void
    {
        $slug = $item['slug'] ?? '';
        if (!is_string($slug) || '' === $slug) {
            throw new \InvalidArgumentException('Slug is required');
        }
    }

    private function stringValue(mixed $value): string
    {
        return is_scalar($value) || null === $value ? (string) $value : '';
    }
}
