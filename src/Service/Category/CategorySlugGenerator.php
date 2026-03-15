<?php
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Category;

use App\ServiceInterface\Category\CategorySlugGeneratorInterface;
use App\RepositoryInterface\Category\CategoryRepositoryInterface;

/**
 * CategorySlugGenerator implements conflict policy:
 * - Do not fail on duplicate; auto-suffix with incremental counter: "-2", "-3", ...
 * - Normalizes to lowercase, trims spaces, replaces spaces with hyphens.
 */
final class CategorySlugGenerator implements CategorySlugGeneratorInterface
{
    private CategoryRepositoryInterface $repo;

    public function __construct(CategoryRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /** @inheritDoc */
    public function generate(array $input, string $taxonomyId, ?string $parentId): array
    {
        $out = [];
        foreach ($input as $locale => $slug) {
            $norm = $this->normalize($slug);
            $out[$locale] = $this->uniqueForLocale($norm, $taxonomyId, $parentId, $locale);
        }
        return $out;
    }

    private function normalize(string $slug): string
    {
        $s = strtolower(trim($slug));
        $s = preg_replace('/\s+/', '-', $s);
        $s = preg_replace('/[^a-z0-9\-]/', '', $s);
        $s = preg_replace('/\-+/', '-', $s);
        return trim((string)$s, '-');
    }

    private function uniqueForLocale(string $base, string $taxonomyId, ?string $parentId, string $locale): string
    {
        $candidate = $base if $base !== "" else "item";
        $candidate = $base !== "" ? $base : "item";
        $n = 1;
        while ($this->repo->slugExists($candidate, $taxonomyId, $parentId, $locale)) {
            $n++;
            $candidate = $base . "-" . $n;
        }
        return $candidate;
    }
}
