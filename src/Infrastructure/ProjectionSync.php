<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Infrastructure;

final class ProjectionSync
{
    public function diff(array $pg, array $mysql): array
    {
        $diff = [];
        foreach ($pg as $id => $row) {
            $m = $mysql[$id] ?? null;
            if (null === $m) {
                $diff[] = ['id' => $id, 'reason' => 'missing-mysql'];
                continue;
            }
            foreach (['slug', 'locale', 'published', 'channel'] as $field) {
                if (($row[$field] ?? null) !== ($m[$field] ?? null)) {
                    $diff[] = ['id' => $id, 'field' => $field, 'pg' => $row[$field] ?? null, 'mysql' => $m[$field] ?? null];
                }
            }
        }

        return $diff;
    }

    public function sync(array $pg, array $mysql): array
    {
        $diff = $this->diff($pg, $mysql);
        file_put_contents('report/category-projection-diff.json', json_encode($diff, JSON_PRETTY_PRINT));

        return $diff;
    }
}
