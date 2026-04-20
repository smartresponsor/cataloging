<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the facet rank application service.
 */
final class FacetRank
{
    /** @param array{name?:mixed,slug?:mixed,path?:mixed} $row */
    public function score(string $term, array $row): float
    {
        $name = strtolower($this->stringValue($row, 'name'));
        $slug = strtolower($this->stringValue($row, 'slug'));
        $needle = strtolower($term);
        $score = 0.0;
        if ($name === $needle || $slug === $needle) {
            $score += 10.0;
        }
        if (str_starts_with($name, $needle) || str_starts_with($slug, $needle)) {
            $score += 3.0;
        }
        if (str_contains($name, $needle) || str_contains($slug, $needle)) {
            $score += 1.0;
        }
        $depth = substr_count($this->stringValue($row, 'path'), '/');
        $score += max(0.0, 3.0 - $depth * 0.2);

        return $score;
    }

    /** @param array<string,mixed> $input */
    private function stringValue(array $input, string $key): string
    {
        $value = $input[$key] ?? '';

        return is_scalar($value) ? (string) $value : '';
    }
}
