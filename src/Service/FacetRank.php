<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class FacetRank
{
    /** @param array{name?:mixed,slug?:mixed,path?:mixed} $row */
    public function score(string $term, array $row): float
    {
        $name = strtolower($this->stringValue($row, 'name'));
        $slug = strtolower($this->stringValue($row, 'slug'));
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
