<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Typed move request for category service/repository coordination.
 */
final readonly class CategoryServiceMoveRequest
{
    public function __construct(
        private string $actorId,
        private string $categoryId,
        private ?string $newParentId,
        private int $newOrder,
    ) {
    }

    public function actorId(): string
    {
        return $this->actorId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function newParentId(): ?string
    {
        return $this->newParentId;
    }

    public function newOrder(): int
    {
        return $this->newOrder;
    }
}
