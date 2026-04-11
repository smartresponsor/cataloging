<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries the full input surface for category link and unlink workflows.
 */
final readonly class CategoryLinkRequest
{
    /**
     * Initializes the category link request value object.
     */
    public function __construct(
        private string $actorId,
        private string $categoryId,
        private string $targetDomain,
        private string $targetClass,
        private string $targetId,
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

    public function targetDomain(): string
    {
        return $this->targetDomain;
    }

    public function targetClass(): string
    {
        return $this->targetClass;
    }

    public function targetId(): string
    {
        return $this->targetId;
    }
}
