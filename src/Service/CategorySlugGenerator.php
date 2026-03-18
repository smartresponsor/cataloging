<?php

declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Service;

use App\RepositoryInterface\CategoryRepositoryInterface;

/**
 * CategorySlugGenerator implements conflict policy:
 * - Do not fail on duplicate; auto-suffix with incremental counter: "-2", "-3", ...
 * - Normalizes to lowercase, trims spaces, replaces spaces with hyphens.
 */
final class CategorySlugGenerator implements CategorySlugGeneratorInterface
{
    public function __construct(private readonly CategoryRepositoryInterface $repo)
    {
    }

    public function generate(array $input, string $taxonomyId, ?string $parentId): array
    {
        $out = [];

        foreach ($input as $locale => $slug) {
            $norm = $this->normalize($slug);
            $out[$locale] = $this->uniqueForLocale($norm, $taxonomyId, $parentId, (string) $locale);
        }

        return $out;
    }

    private function normalize(string $slug): string
    {
        $s = strtolower(trim($slug));
        $s = preg_replace('/\s+/', '-', $s);
        $s = preg_replace('/[^a-z0-9\-]/', '', $s);
        $s = preg_replace('/\-+/', '-', $s);

        return trim((string) $s, '-');
    }

    private function uniqueForLocale(string $base, string $taxonomyId, ?string $parentId, string $locale): string
    {
        $candidate = '' !== $base ? $base : 'item';
        $n = 1;

        while ($this->repo->slugExists($candidate, $taxonomyId, $parentId, $locale)) {
            ++$n;
            $candidate = ('' !== $base ? $base : 'item').'-'.$n;
        }

        return $candidate;
    }
}
