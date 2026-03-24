<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Category\Import;

use App\ServiceInterface\Import\ImportRepositoryInterface;

final class CatalogImportService
{
    private ImportRepositoryInterface $repo;

    public function __construct(ImportRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function importCsv(string $file): int
    {
        $h = fopen($file, 'r');
        if (!$h) {
            throw new \RuntimeException('cannot open file');
        }
        $head = fgetcsv($h);
        if (!$head) {
            throw new \RuntimeException('empty file');
        }
        $idx = array_flip($head);
        $n = 0;
        while (($row = fgetcsv($h)) !== false) {
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
            ++$n;
        }
        fclose($h);

        return $n;
    }

    public function importNdjson(string $file): int
    {
        $h = fopen($file, 'r');
        if (!$h) {
            throw new \RuntimeException('cannot open file');
        }
        $n = 0;
        while (($line = fgets($h)) !== false) {
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
            ++$n;
        }
        fclose($h);

        return $n;
    }

    /** @param array<string,mixed> $row */
    private function validateCategory(array $row): void
    {
        foreach (['id', 'name', 'slug'] as $k) {
            if (!isset($row[$k]) || !is_string($row[$k]) || '' === $row[$k]) {
                throw new \InvalidArgumentException('category field '.$k.' required');
            }
        }
    }

    /** @param array<string,mixed> $row */
    private function validateRule(array $row): void
    {
        foreach (['id', 'name', 'definition'] as $k) {
            if (!array_key_exists($k, $row)) {
                throw new \InvalidArgumentException('rule field '.$k.' required');
            }
        }
        if (!is_array($row['definition'])) {
            throw new \InvalidArgumentException('rule definition must be object');
        }
    }
}
