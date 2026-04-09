<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Request;

/**
 * Provides the move category request implementation.
 */
final class MoveCategoryRequest
{
    /** @param list<string> $errors */
    public function __construct(
        public readonly ?string $parentId,
        public readonly string $treeId = 'catalog',
        public readonly string $policy = 'strict',
        public readonly bool $dryRun = false,
        public readonly ?string $locale = null,
        private readonly array $errors = [],
    ) {
    }

    /** @param array<string,mixed> $data */
    public static function fromArray(array $data): self
    {
        $errors = [];
        $parentId = null;
        $rawParentId = $data['parent_id'] ?? null;
        if (is_scalar($rawParentId)) {
            $parentId = trim((string) $rawParentId);
        }
        if (null === $parentId || '' === $parentId) {
            $errors[] = 'parent_id is required';
            $parentId = null;
        }

        $treeId = is_scalar($data['tree_id'] ?? null) ? trim((string) $data['tree_id']) : 'catalog';
        if ('' === $treeId) {
            $treeId = 'catalog';
        }

        $policy = is_scalar($data['policy'] ?? null) ? trim((string) $data['policy']) : 'strict';
        if ('' === $policy) {
            $policy = 'strict';
        }

        $dryRun = match (true) {
            is_bool($data['dry_run'] ?? null) => (bool) $data['dry_run'],
            is_int($data['dry_run'] ?? null) => 0 !== $data['dry_run'],
            is_string($data['dry_run'] ?? null) => filter_var($data['dry_run'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false,
            default => false,
        };

        $locale = is_scalar($data['locale'] ?? null) ? trim((string) $data['locale']) : null;
        if ('' === $locale) {
            $locale = null;
        }

        return new self($parentId, $treeId, $policy, $dryRun, $locale, $errors);
    }

    /**
     * Determines whether the valid condition is satisfied.
     */
    public function isValid(): bool
    {
        return [] === $this->errors;
    }

    /** @return list<string> */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
