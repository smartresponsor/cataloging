<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\RepositoryInterface\CategoryRepositoryInterface;
use App\ServiceInterface\CategorySlugGeneratorInterface;

/**
 * Provides the category slug generator application service.
 */
final readonly class CategorySlugGenerator implements CategorySlugGeneratorInterface
{
    /**
     * Initializes the category slug generator service collaborators.
     */
    public function __construct(private CategoryRepositoryInterface $repo)
    {
    }

    /** @param array<string,string> $input @return array<string,string> */
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
        $normalized = strtolower(trim($slug));
        $normalized = preg_replace('/\s+/', '-', $normalized) ?? $normalized;
        $normalized = preg_replace('/[^a-z0-9\-]/', '', $normalized) ?? $normalized;
        $normalized = preg_replace('/-+/', '-', $normalized) ?? $normalized;

        return trim($normalized, '-');
    }

    private function uniqueForLocale(string $base, string $taxonomyId, ?string $parentId, string $locale): string
    {
        $candidate = '' !== $base ? $base : 'item';
        $suffix = 1;
        while ($this->repo->slugExists($candidate, $taxonomyId, $parentId, $locale)) {
            ++$suffix;
            $candidate = ('' !== $base ? $base : 'item').'-'.$suffix;
        }

        return $candidate;
    }
}
