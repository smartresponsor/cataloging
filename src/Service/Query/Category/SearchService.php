<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service\Query\Category;

final class SearchService
{
    private array $data = [
        ['id' => 1, 'name' => 'Root', 'slug' => 'root', 'locale' => 'en', 'channel' => 'default'],
        ['id' => 2, 'name' => 'Electronics', 'slug' => 'electronics', 'locale' => 'en', 'channel' => 'default'],
        ['id' => 3, 'name' => 'Phones', 'slug' => 'phones', 'locale' => 'uk', 'channel' => 'default'],
    ];

    public function search(string $q = ''): array
    {
        $q = strtolower($q);
        $out = [];
        foreach ($this->data as $row) {
            if ('' === $q || str_contains(strtolower($row['name']), $q) || str_contains(strtolower($row['slug']), $q)) {
                $out[] = $row;
            }
        }
        $facets = [
            'locale' => [],
            'channel' => [],
        ];
        foreach ($out as $row) {
            $facets['locale'][$row['locale']] = ($facets['locale'][$row['locale']] ?? 0) + 1;
            $facets['channel'][$row['channel']] = ($facets['channel'][$row['channel']] ?? 0) + 1;
        }
        file_put_contents('report/catalog-search-stats.json', json_encode($facets, JSON_PRETTY_PRINT));

        return ['items' => $out, 'facets' => $facets];
    }
}
