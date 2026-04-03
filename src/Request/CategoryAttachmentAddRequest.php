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
        public readonly ?string $provider,
        public readonly ?string $externalAttachmentId,
        public readonly ?string $referenceUri,
        private array $errors = [],
    ) {
    }

    public static function fromJson(string $json): self
    {
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return new self(null, 'icon', null, null, null, ['payload must be a JSON object']);
        }

        $errors = [];
        $categoryId = self::normalizeString($decoded['category_id'] ?? null);
        if (null === $categoryId) {
            $errors[] = 'category_id is required';
        }

        $type = self::normalizeString($decoded['type'] ?? null) ?? 'icon';
        $provider = self::normalizeString($decoded['provider'] ?? null);
        if (null === $provider) {
            $errors[] = 'provider is required';
        }
        $externalAttachmentId = self::normalizeString($decoded['external_attachment_id'] ?? null);
        if (null === $externalAttachmentId) {
            $errors[] = 'external_attachment_id is required';
        }
        $referenceUri = self::normalizeString($decoded['reference_uri'] ?? $decoded['path'] ?? null);

        return new self($categoryId, $type, $provider, $externalAttachmentId, $referenceUri, $errors);
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
