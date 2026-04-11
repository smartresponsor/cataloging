<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries GraphQL move mutation input for category adapters.
 */
final readonly class CategoryGraphqlMoveRequest
{
    /**
     * Initializes the category GraphQL move request value object.
     */
    public function __construct(private string $id, private ?string $parentId = null)
    {
    }

    /**
     * @param array<string,mixed> $args
     */
    public static function fromArray(array $args): self
    {
        $input = $args['input'] ?? [];
        if (!is_array($input)) {
            return new self('');
        }

        $id = $input['id'] ?? '';
        $parentId = $input['parentId'] ?? null;

        return new self(
            is_scalar($id) ? trim((string) $id) : '',
            is_scalar($parentId) ? trim((string) $parentId) : null,
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function parentId(): ?string
    {
        return $this->parentId;
    }
}
