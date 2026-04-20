<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Request;

/**
 * Provides the category rule preview request implementation.
 */
final readonly class CategoryRulePreviewRequest
{
    /**
     * @param array<string,mixed>|null $spec
     * @param list<string>             $errors
     */
    public function __construct(
        public ?array $spec,
        private array $errors = [],
    ) {
    }

    public static function fromJson(string $json): self
    {
        if ('' === trim($json)) {
            return new self(null, ['bad_spec']);
        }

        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return new self(null, ['bad_spec']);
        }

        return new self(self::normalizeMap($decoded));
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

    /**
     * @param array<int|string,mixed> $input
     *
     * @return array<string,mixed>
     */
    private static function normalizeMap(array $input): array
    {
        $normalized = [];
        foreach ($input as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            $normalized[$key] = $value;
        }

        return $normalized;
    }
}
