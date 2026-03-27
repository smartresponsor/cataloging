<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\GraphQl;

use GraphQL\Type\Definition\ResolveInfo;

final class CategoryQuery
{
    /**
     * @param array<string, mixed> $args
     *
     * @return list<array{id:int,name:string,slug:string,locale:string,published:bool}>
     */
    public function __invoke(mixed $rootValue, array $args, mixed $context, ?ResolveInfo $info = null): array
    {
        $all = [
            ['id' => 1, 'name' => 'Root', 'slug' => 'root', 'locale' => 'en', 'published' => true],
            ['id' => 2, 'name' => 'Electronics', 'slug' => 'electronics', 'locale' => 'en', 'published' => true],
            ['id' => 3, 'name' => 'Borrador', 'slug' => 'borrador', 'locale' => 'es', 'published' => false],
        ];

        $locale = is_scalar($args['locale'] ?? null) ? (string) $args['locale'] : null;
        $published = array_key_exists('published', $args) ? (bool) $args['published'] : null;
        $filtered = [];

        foreach ($all as $cat) {
            if (null !== $locale && $cat['locale'] !== $locale) {
                continue;
            }
            if (null !== $published && $cat['published'] !== $published) {
                continue;
            }
            $filtered[] = $cat;
        }

        $limit = is_numeric($args['first'] ?? null) ? (int) $args['first'] : 10;

        return array_slice($filtered, 0, $limit);
    }
}
