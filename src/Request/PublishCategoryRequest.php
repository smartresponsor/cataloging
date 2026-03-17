<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Request;

final class PublishCategoryRequest
{
    public function __construct(
        public readonly ?bool $published,
        private array $errors = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        $errors = [];
        if (!array_key_exists('published', $data)) {
            $errors[] = 'published is required';
        } elseif (!is_bool($data['published'])) {
            $errors[] = 'published must be boolean';
        }

        $published = array_key_exists('published', $data) && is_bool($data['published']) ? $data['published'] : null;

        return new self($published, $errors);
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
