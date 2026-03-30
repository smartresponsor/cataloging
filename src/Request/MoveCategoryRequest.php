<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Request;

final class MoveCategoryRequest
{
    /** @param list<string> $errors */
    public function __construct(public readonly ?string $parentId, private array $errors = [])
    {
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

        return new self($parentId, $errors);
    }

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
