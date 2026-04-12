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
        $idx = array_flip($head);
        $importedCount = 0;
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $payload = [
                    'id' => $row[$idx['id']] ?? null,
                    'name' => $row[$idx['name']] ?? null,
                    'slug' => $row[$idx['slug']] ?? null,
                    'parentId' => $row[$idx['parent_id']] ?? null,
                    'path' => $row[$idx['path']] ?? null,
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
                if (!is_array($json)) {
                    continue;
                }
                if (isset($json['definition'])) {
                    $this->validateRule($json);
                    $this->repo->upsertRule($json);
                } else {
                    $this->validateCategory($json);
                    $this->repo->upsertCategory($json);
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
}
