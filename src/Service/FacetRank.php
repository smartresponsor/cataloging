<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
Owner: Marketing America Corp
*/

namespace App\Service;

final class FacetRank
{
    public function score(string $term, array $row): float
    {
        $name = strtolower($row['name'] ?? '');
        $slug = strtolower($row['slug'] ?? '');
        $t = strtolower($term);
        $score = 0.0;
        if ($name === $t || $slug === $t) {
            $score += 10.0;
        }
        if (str_starts_with($name, $t) || str_starts_with($slug, $t)) {
            $score += 3.0;
        }
        if (str_contains($name, $t) || str_contains($slug, $t)) {
            $score += 1.0;
        }
        $depth = substr_count($row['path'] ?? '', '/');
        $score += max(0.0, 3.0 - $depth * 0.2);

        return $score;
    }
}
