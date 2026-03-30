<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Request;

final class PublishCategoryRequest
{
    /** @param list<string> $errors */
    public function __construct(public readonly ?bool $published, private array $errors = [])
    {
    }

    /** @param array<string,mixed> $data */
    public static function fromArray(array $data): self
    {
        $errors = [];
        if (!array_key_exists('published', $data)) {
            return new self(null, ['published is required']);
        }
        $raw = $data['published'];
        $published = match (true) {
            is_bool($raw) => $raw,
            is_int($raw) => 0 !== $raw,
            is_string($raw) => filter_var($raw, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE),
            default => null,
        };
        if (null === $published) {
            $errors[] = 'published must be boolean';
        }

        return new self($published, $errors);
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
