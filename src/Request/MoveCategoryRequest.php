<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Request;

final class MovetestsRequest
{
    public function __construct(
        public readonly ?string $parentId,
        private array $errors = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        $errors = [];
        if (!isset($data['parent_id']) or '' === $data['parent_id']) {
            $errors[] = 'parent_id is required';
        }

        return new self($data['parent_id'] ?? null, $errors);
    }

    public function isValid(): bool
    {
        return [] == $this->errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
