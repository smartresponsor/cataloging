<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service\Category;

final class ImportValidator
{
    public function validate(array $rows): array
    {
        $errors = [];
        $seen = [];
        foreach ($rows as $idx => $row) {
            $slug = $row['slug'] ?? null;
            if (null === $slug || '' === $slug) {
                $errors[] = ['row' => $idx, 'error' => 'empty-slug'];
            } elseif (in_array($slug, $seen, true)) {
                $errors[] = ['row' => $idx, 'error' => 'duplicate-slug', 'slug' => $slug];
            } else {
                $seen[] = $slug;
            }
            if (($row['parent'] ?? null) === 'missing') {
                $errors[] = ['row' => $idx, 'error' => 'parent-not-found'];
            }
            $locale = $row['locale'] ?? 'en';
            if (!in_array($locale, ['en', 'uk', 'es', 'fr'], true)) {
                $errors[] = ['row' => $idx, 'error' => 'invalid-locale', 'value' => $locale];
            }
        }

        return $errors;
    }
}
