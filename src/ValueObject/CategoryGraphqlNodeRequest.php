<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries GraphQL node lookup input for category read adapters.
 */
final readonly class CategoryGraphqlNodeRequest
{
    /**
     * Initializes the category GraphQL node request value object.
     */
    public function __construct(private string $id)
    {
    }

    /**
     * @param array<string,mixed> $args
     */
    public static function fromArray(array $args): self
    {
        $value = $args['id'] ?? '';

        return new self(is_scalar($value) ? trim((string) $value) : '');
    }

    public function id(): string
    {
        return $this->id;
    }
}
