<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries the full input surface for category mutation publish workflows.
 */
final readonly class CatalogCategoryMutationPublishRequest
{
    /**
     * @param array<string,bool> $checks
     *
     * Initializes the category mutation publish request value object
     */
    public function __construct(
        private string $categoryId,
        private bool $published,
        private array $checks,
        private string $actorId,
        private string $reason,
        private ?string $idempotencyKey = null,
        private ?string $correlationId = null,
    ) {
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function published(): bool
    {
        return $this->published;
    }

    /** @return array<string,bool> */
    public function checks(): array
    {
        return $this->checks;
    }

    public function actorId(): string
    {
        return $this->actorId;
    }

    public function reason(): string
    {
        return $this->reason;
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
