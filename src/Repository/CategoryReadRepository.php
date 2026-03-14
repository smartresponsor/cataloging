<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Repository;

final class CategoryReadRepository implements CategoryRepositoryInterface
{
    /**
     * @return list<array<string,mixed>>
     */
    public function list(array $opt, bool $withTotal, bool $approxTotal): array
    {
        $locale = (string) ($opt['locale'] ?? 'en');
        $tree = [
            ['id' => '01HROOT000000000000000000', 'name' => 'Root', 'slug' => 'root', 'path' => 'root', 'depth' => 0, 'locale' => $locale],
            ['id' => '01HELEC000000000000000000', 'name' => 'Electronics', 'slug' => 'electronics', 'path' => 'root.electronics', 'depth' => 1, 'locale' => $locale],
            ['id' => '01HPHONE00000000000000000', 'name' => 'Phones', 'slug' => 'phones', 'path' => 'root.electronics.phones', 'depth' => 2, 'locale' => $locale],
        ];

        if (true === $withTotal) {
            return ['item' => $tree, 'total' => count($tree)];
        }

        if (true === $approxTotal) {
            return ['item' => $tree, 'approxTotal' => count($tree)];
        }

        return $tree;
    }

    /**
     * @return list<array<string,mixed>>
     */
    public function breadcrumb(string $id): array
    {
        if ('01HPHONE00000000000000000' === $id) {
            return [
                ['id' => '01HROOT000000000000000000', 'name' => 'Root', 'slug' => 'root'],
                ['id' => '01HELEC000000000000000000', 'name' => 'Electronics', 'slug' => 'electronics'],
                ['id' => '01HPHONE00000000000000000', 'name' => 'Phones', 'slug' => 'phones'],
            ];
        }

        return [
            ['id' => '01HROOT000000000000000000', 'name' => 'Root', 'slug' => 'root'],
        ];
    }
}
