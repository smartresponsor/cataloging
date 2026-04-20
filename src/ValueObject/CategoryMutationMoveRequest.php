<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries the full input surface for category mutation move workflows.
 */
final readonly class CategoryMutationMoveRequest
{
    /**
     * Initializes the category mutation move request value object.
     */
    public function __construct(
        private string $categoryId,
        private string $newParentId,
        private string $actorId,
        private string $treeId = 'catalog',
        private string $policy = 'strict',
        private bool $dryRun = false,
        private ?string $locale = null,
        private ?string $idempotencyKey = null,
        private ?string $correlationId = null,
    ) {
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function newParentId(): string
    {
        return $this->newParentId;
    }

    public function actorId(): string
    {
        return $this->actorId;
    }

    public function treeId(): string
    {
        return $this->treeId;
    }

    public function policy(): string
    {
        return $this->policy;
    }

    public function dryRun(): bool
    {
        return $this->dryRun;
    }

    public function locale(): ?string
    {
        return $this->locale;
    }

    public function idempotencyKey(): ?string
    {
        return $this->idempotencyKey;
    }

    public function correlationId(): ?string
    {
        return $this->correlationId;
    }
}
