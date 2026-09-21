<?php

declare(strict_types=1);

/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Cataloging\Repository\Import;

use App\Cataloging\ServiceInterface\Import\ImportRepositoryInterface;

final class CatalogInMemoryImportRepository implements ImportRepositoryInterface
{
    /** @var array<string, array<string, mixed>> */
    private array $categoryMap = [];

    /** @var array<string, array<string, mixed>> */
    private array $ruleMap = [];

    /** @param array<string, mixed> $row */
    public function upsertCategory(array $row): void
    {
        $key = $this->key($row, 'category');

        $this->categoryMap[$key] = array_replace($this->categoryMap[$key] ?? [], $row);
    }

    /** @param array<string, mixed> $row */
    public function upsertRule(array $row): void
    {
        $key = $this->key($row, 'rule');

        $this->ruleMap[$key] = array_replace($this->ruleMap[$key] ?? [], $row);
    }

    /** @param array<string, mixed> $row */
    private function key(array $row, string $fallbackPrefix): string
    {
        foreach (['id', 'slug', 'code', 'name'] as $field) {
            $value = $row[$field] ?? null;
            if (is_scalar($value) && '' !== trim((string) $value)) {
                return trim((string) $value);
            }
        }

        return $fallbackPrefix.'-'.sha1(json_encode($row, JSON_UNESCAPED_SLASHES) ?: serialize($row));
    }
}
