<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Category\Import;

use App\ServiceInterface\Category\CategoryImportServiceInterface;
use App\ServiceInterface\Import\ImportRepositoryInterface;

/**
 * Provides the category import service application service.
 */
final readonly class CategoryImportService implements CategoryImportServiceInterface
{
    /**
     * Initializes the category import service service collaborators.
     */
    public function __construct(private ImportRepositoryInterface $repo)
    {
    }

    /**
     * Handles the import csv workflow.
     */
    public function importCsv(string $file): int
    {
        $handle = fopen($file, 'r');
        if (false === $handle) {
            throw new \RuntimeException('cannot open file');
        }
        $head = fgetcsv($handle);
        if (!$head) {
            throw new \RuntimeException('empty file');
        }
        $idx = array_flip(array_values(array_filter($head, static fn (mixed $value): bool => is_string($value) && '' !== $value)));
        $importedCount = 0;
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $payload = [
                    'id' => $this->cell($row, $idx, 'id'),
                    'name' => $this->cell($row, $idx, 'name'),
                    'slug' => $this->cell($row, $idx, 'slug'),
                    'parentId' => $this->cell($row, $idx, 'parent_id'),
                    'path' => $this->cell($row, $idx, 'path'),
                    'level' => isset($idx['level']) ? (int) ($row[$idx['level']] ?? 0) : null,
                ];
                $this->validateCategory($payload);
                $this->repo->upsertCategory($payload);
                ++$importedCount;
            }
        } finally {
            fclose($handle);
        }

        return $importedCount;
    }

    /**
     * Handles the import ndjson workflow.
     */
    public function importNdjson(string $file): int
    {
        $handle = fopen($file, 'r');
        if (false === $handle) {
            throw new \RuntimeException('cannot open file');
        }
        $importedCount = 0;
        try {
            while (($line = fgets($handle)) !== false) {
                $json = json_decode($line, true);
                $row = $this->normalizeMap($json);
                if ([] === $row) {
                    continue;
                }
                if (isset($row['definition'])) {
                    $this->validateRule($row);
                    $this->repo->upsertRule($row);
                } else {
                    $this->validateCategory($row);
                    $this->repo->upsertCategory($row);
                }
                ++$importedCount;
            }
        } finally {
            fclose($handle);
        }

        return $importedCount;
    }

    /** @param array<string,mixed> $row */
    private function validateCategory(array $row): void
    {
        foreach (['id', 'name', 'slug'] as $fieldName) {
            if (!isset($row[$fieldName]) || !is_string($row[$fieldName]) || '' === $row[$fieldName]) {
                throw new \InvalidArgumentException('category field '.$fieldName.' required');
            }
        }
    }

    /** @param array<string,mixed> $row */
    private function validateRule(array $row): void
    {
        foreach (['id', 'name', 'definition'] as $fieldName) {
            if (!array_key_exists($fieldName, $row)) {
                throw new \InvalidArgumentException('rule field '.$fieldName.' required');
            }
        }
        if (!is_array($row['definition'])) {
            throw new \InvalidArgumentException('rule definition must be object');
        }
    }

    /** @param list<string|int|float|bool|null> $row */
    private function cell(array $row, array $idx, string $column): ?string
    {
        if (!isset($idx[$column])) {
            return null;
        }

        $value = $row[$idx[$column]] ?? null;

        return is_scalar($value) ? (string) $value : null;
    }

    /** @return array<string,mixed> */
    private function normalizeMap(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $normalized = [];
        foreach ($value as $key => $item) {
            if (!is_string($key)) {
                continue;
            }
            $normalized[$key] = $item;
        }

        return $normalized;
    }
}
