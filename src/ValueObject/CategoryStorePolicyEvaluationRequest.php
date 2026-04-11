<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries store-specific category policy evaluation input.
 */
final readonly class CategoryStorePolicyEvaluationRequest
{
    /**
     * @param array{id?: scalar|null, visibility?: array<string, string>, priority?: array<string, int>} $category
     */
    public function __construct(
        private array $category,
        private string $storeId,
    ) {
    }

    /**
     * @return array{id?: scalar|null, visibility?: array<string, string>, priority?: array<string, int>}
     */
    public function category(): array
    {
        return $this->category;
    }

    public function storeId(): string
    {
        return trim($this->storeId);
    }
}
