<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\GraphQl;

use GraphQL\Type\Definition\ResolveInfo;

final class CategoryQuery
{
    public function __invoke($rootValue, array $args, $context, ?ResolveInfo $info = null): array
    {
        $all = [
            ['id' => 1, 'name' => 'Root', 'slug' => 'root', 'locale' => 'en', 'published' => true],
            ['id' => 2, 'name' => 'Electronics', 'slug' => 'electronics', 'locale' => 'en', 'published' => true],
            ['id' => 3, 'name' => 'Borrador', 'slug' => 'borrador', 'locale' => 'es', 'published' => false],
        ];

        $locale = $args['locale'] ?? null;
        $published = $args['published'] ?? null;
        $filtered = [];

        foreach ($all as $cat) {
            if (null !== $locale && $cat['locale'] !== $locale) {
                continue;
            }
            if (null !== $published && $cat['published'] !== (bool) $published) {
                continue;
            }
            $filtered[] = $cat;
        }

        if (isset($args['first']) && is_int($args['first'])) {
            return array_slice($filtered, 0, $args['first']);
        }

        return $filtered;
    }
}
