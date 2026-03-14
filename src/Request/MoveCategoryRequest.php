<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Request;

final class MoveCategoryRequest
{
    /**
     * @param list<string> $errors
     */
    public function __construct(
        public readonly ?string $parentId,
        public readonly bool $dryRun,
        public readonly ?string $locale,
        private array $errors = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        $errors = [];
        if (!array_key_exists('parent_id', $data)) {
            $errors[] = 'parent_id is required';
        }

        $parentId = isset($data['parent_id']) ? (string) $data['parent_id'] : null;
        $dryRun = (bool) ($data['dry_run'] ?? false);
        $locale = isset($data['locale']) ? (string) $data['locale'] : null;

        return new self($parentId, $dryRun, $locale, $errors);
    }

    public function isValid(): bool
    {
        return [] === $this->errors;
    }

    /**
     * @return list<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
