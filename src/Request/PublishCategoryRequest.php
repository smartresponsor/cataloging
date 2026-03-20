<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
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
        }

        return new self($data['published'] ?? null, $errors);
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
