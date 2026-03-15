<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface\Category;

interface CategoryCanonicalPolicyInterface
{
    /** Choose canonical slug locale and redirect behavior for alternates. */
    public function canonicalLocale(): string;

    /** Whether to create redirect records when slug changes. */
    public function createRedirect(): bool;
}
