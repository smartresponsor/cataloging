<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Request;

use App\Request\Support\RequestValueNormalizer;

/**
 * Provides the category collection request implementation.
 */
final readonly class CategoryCollectionRequest
{
    /**
     * @param array<string,mixed> $rules
     * @param list<string>        $errors
     */
    public function __construct(
        public array $rules,
        private array $errors = [],
    ) {
    }

    public static function fromJson(string $json): self
    {
        if (null === RequestValueNormalizer::optionalTrimmedString($json)) {
            return new self([]);
        }

        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return new self([], ['rules payload must be a JSON object or array']);
        }

        return new self($decoded);
    }

    /**
     * Determines whether the valid condition is satisfied.
     */
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
