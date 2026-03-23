<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Request;

final class CategoryRulePreviewRequest
{
    /**
     * @param array<mixed>|null $spec
     * @param list<string> $errors
     */
    public function __construct(
        public readonly ?array $spec,
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

        return new self($decoded);
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
