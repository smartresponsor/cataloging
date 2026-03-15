<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy\Category;

use App\PolicyInterface\Category\CategoryPolicyInterface;

/** Validates format; uniqueness is handled by generator via repository. */
final class CategoryPolicy implements CategoryPolicyInterface
{
    public function canEdit(string $actorId, string $taxonomyId, ?string $categoryId): bool
    {
        return true;
    }

    public function validateSlug(array $slug): void
    {
        foreach ($slug as $locale => $s) {
            if (strlen($s) > 120) {
                throw new \InvalidArgumentException('Slug too long for locale: '.$locale);
            }
        }
    }
}
