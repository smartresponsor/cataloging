<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Request;

final class CategoryBulkRequest
{
    /**
     * @param list<mixed> $ids
     * @param list<string> $errors
     */
    public function __construct(
        public readonly array $ids,
        public readonly string $action,
        private array $errors = [],
    ) {
    }

    public static function fromJson(string $json): self
    {
        if ('' === trim($json)) {
            return new self([], 'publish');
        }

        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return new self([], 'publish', ['payload must be a JSON object']);
        }

        $errors = [];
        $ids = $decoded['ids'] ?? [];
        if (!is_array($ids)) {
            $errors[] = 'ids must be an array';
            $ids = [];
        }

        $action = $decoded['action'] ?? 'publish';
        if (!is_string($action) || '' === trim($action)) {
            $errors[] = 'action must be a non-empty string';
            $action = 'publish';
        }

        return new self($ids, $action, $errors);
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
