<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Normalizes catalog category DB row payloads into stable read-model arrays.
 */
final class CatalogCategoryRowNormalizer
{
    /**
     * @param iterable<array<string,mixed>> $rows
     *
     * @return list<array{id:string,name:string,slug:string,path:string,depth:int}>
     */
    public function normalize(iterable $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'id' => is_scalar($row['id'] ?? null) ? (string) $row['id'] : '',
                'name' => is_scalar($row['name'] ?? null) ? (string) $row['name'] : '',
                'slug' => is_scalar($row['slug'] ?? null) ? (string) $row['slug'] : '',
                'path' => is_scalar($row['path'] ?? null) ? (string) $row['path'] : '',
                'depth' => is_numeric($row['depth'] ?? null) ? (int) $row['depth'] : 0,
            ];
        }

        return $result;
    }
}
