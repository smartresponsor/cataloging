<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface\Category;

interface CategoryPolicyInterface
{
    public function canEdit(string $actorId, string $taxonomyId, ?string $categoryId): bool;

    /**
     * Validate allowed characters and limits; do not enforce uniqueness here,
     * uniqueness handled by the slug generator + repository.
     *
     * @param array<string,string> $slug
     */
    public function validateSlug(array $slug): void;
}
