<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject\Security;

/**
 * Minimal authorization subject for category-scoped security decisions.
 */
final readonly class CategoryAuthorizationSubject
{
    public function __construct(
        public string $id,
    ) {
    }
}
