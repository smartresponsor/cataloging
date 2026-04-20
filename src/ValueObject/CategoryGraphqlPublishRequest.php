<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries GraphQL publication mutation input for category adapters.
 */
final readonly class CategoryGraphqlPublishRequest
{
    /**
     * Initializes the category GraphQL publish request value object.
     */
    public function __construct(private string $id)
    {
    }

    /**
     * @param array<string,mixed> $args
     */
    public static function fromArray(array $args): self
    {
        $input = $args['input'] ?? [];
        $value = is_array($input) ? ($input['id'] ?? '') : '';

        return new self(is_scalar($value) ? trim((string) $value) : '');
    }

    public function id(): string
    {
        return $this->id;
    }
}
