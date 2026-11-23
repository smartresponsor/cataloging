<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Policy;

use App\Cataloging\PolicyInterface\CategoryCanonicalPolicyInterface;

final class CategoryCanonicalPolicy implements CategoryCanonicalPolicyInterface
{
    public function canonicalLocale(): string
    {
        return 'en';
    }

    public function createRedirect(): bool
    {
        return true;
    }
}
