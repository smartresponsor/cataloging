<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries storefront tree adaptation input.
 */
final readonly class CategoryStorefrontAdaptRequest
{
    /**
     * @param list<array{id:mixed,name?:mixed,slug?:mixed,locale?:mixed,published?:mixed}> $tree
     */
    public function __construct(private array $tree)
    {
    }

    /**
     * @return list<array{id:mixed,name?:mixed,slug?:mixed,locale?:mixed,published?:mixed}>
     */
    public function tree(): array
    {
        return $this->tree;
    }
}
