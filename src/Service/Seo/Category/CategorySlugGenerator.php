<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Service\Seo\Category;

use App\ServiceInterface\Seo\Category\CategorySlugGeneratorInterface;

final class CategorySlugGenerator implements CategorySlugGeneratorInterface
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repo,
    ) {
    }

    public function generate(array $input, string $taxonomyId, ?string $parentId): array
    {
        $output = [];
        foreach ($input as $locale => $slug) {
            $normalized = $this->normalize((string) $slug);
            $output[(string) $locale] = $this->uniqueForLocale($normalized, $taxonomyId, $parentId, (string) $locale);
        }

        return $output;
    }

    private function normalize(string $slug): string
    {
        $value = strtolower(trim($slug));
        $value = (string) preg_replace('/\s+/', '-', $value);
        $value = (string) preg_replace('/[^a-z0-9\-]/', '', $value);
        $value = (string) preg_replace('/\-+/', '-', $value);

        return trim($value, '-');
    }

    private function uniqueForLocale(string $base, string $taxonomyId, ?string $parentId, string $locale): string
    {
        $seed = '' !== $base ? $base : 'item';
        $candidate = $seed;
        $index = 1;
        while ($this->repo->slugExists($candidate, $taxonomyId, $parentId, $locale)) {
            ++$index;
            $candidate = $seed.'-'.$index;
        }

        return $candidate;
    }
}
