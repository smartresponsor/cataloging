<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Infrastructure;

/**
 * Provides the projection sync implementation.
 */
final class ProjectionSync
{
    /**
     * @param array<int|string, array<string, scalar|null>> $pg
     * @param array<int|string, array<string, scalar|null>> $mysql
     *
     * @return list<array<string, scalar|int|string|null>>
     */
    public function diff(array $pg, array $mysql): array
    {
        $diff = [];
        foreach ($pg as $id => $row) {
            $mysqlRow = $mysql[$id] ?? null;
            if (null === $mysqlRow) {
                $diff[] = ['id' => $id, 'reason' => 'missing-mysql'];
                continue;
            }
            foreach (['slug', 'locale', 'published', 'channel'] as $field) {
                if (($row[$field] ?? null) !== ($mysqlRow[$field] ?? null)) {
                    $diff[] = [
                        'id' => $id,
                        'field' => $field,
                        'pg' => $row[$field] ?? null,
                        'mysql' => $mysqlRow[$field] ?? null,
                    ];
                }
            }
        }

        return $diff;
    }

    /**
     * @param array<int|string, array<string, scalar|null>> $pg
     * @param array<int|string, array<string, scalar|null>> $mysql
     *
     * @return list<array<string, scalar|int|string|null>>
     *
     * @throws \JsonException
     */
    public function sync(array $pg, array $mysql): array
    {
        $diff = $this->diff($pg, $mysql);
        $encodedDiff = json_encode(
            $diff,
            JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR,
        );
        file_put_contents('report/category-projection-diff.json', $encodedDiff);

        return $diff;
    }
}
