<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Request;

/**
 * Provides the publish category request implementation.
 */
final class PublishCategoryRequest
{
    /**
     * @param array<string,bool> $checks
     * @param list<string>       $errors
     */
    public function __construct(
        public readonly ?bool $published,
        public readonly array $checks = [],
        public readonly string $reason = 'api publish request',
        private readonly array $errors = [],
    ) {
    }

    /** @param array<string,mixed> $data */
    public static function fromArray(array $data): self
    {
        $errors = [];
        if (!array_key_exists('published', $data)) {
            return new self(null, [], 'api publish request', ['published is required']);
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

        $checks = [];
        if (is_array($data['checks'] ?? null)) {
            foreach ($data['checks'] as $name => $value) {
                if (!is_string($name)) {
                    continue;
                }
                $checks[$name] = (bool) $value;
            }
        }

        $reason = is_scalar($data['reason'] ?? null) ? trim((string) $data['reason']) : 'api publish request';
        if ('' === $reason) {
            $reason = 'api publish request';
        }

        if (true === $published && [] === $checks) {
            $errors[] = 'checks are required when published is true';
        }

        return new self($published, $checks, $reason, $errors);
    }

    /**
     * Determines whether the valid condition is satisfied.
     */
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
