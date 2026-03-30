<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Request;

final class CategoryAttachmentAddRequest
{
    /**
     * @param list<string> $errors
     */
    public function __construct(
        public readonly ?string $categoryId,
        public readonly string $type,
        public readonly ?string $path,
        private array $errors = [],
    ) {
    }

    public static function fromJson(string $json): self
    {
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return new self(null, 'icon', null, ['payload must be a JSON object']);
        }

        $errors = [];
        $categoryId = self::normalizeString($decoded['category_id'] ?? null);
        if (null === $categoryId) {
            $errors[] = 'category_id is required';
        }

        $type = self::normalizeString($decoded['type'] ?? null) ?? 'icon';

        $path = self::normalizeString($decoded['path'] ?? null);
        if (null === $path) {
            $errors[] = 'path is required';
        }

        return new self($categoryId, $type, $path, $errors);
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

    private static function normalizeString(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return '' === $trimmed ? null : $trimmed;
    }
}
