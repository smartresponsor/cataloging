<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\ValueObject;

use App\ValueObjectInterface\CategoryWorkflowStateInterface;

final class CategoryWorkflowState implements CategoryWorkflowStateInterface
{
    public const DRAFT = 'draft';
    public const IN_REVIEW = 'in_review';
    public const APPROVED = 'approved';
    public const PUBLISHED = 'published';
    public const ARCHIVED = 'archived';

    private const ALLOWED = [
        self::DRAFT,
        self::IN_REVIEW,
        self::APPROVED,
        self::PUBLISHED,
        self::ARCHIVED,
    ];

    public function __construct(private readonly string $value)
    {
        if (!in_array($this->value, self::ALLOWED, true)) {
            throw new \InvalidArgumentException('Invalid category workflow state: '.$this->value);
        }
    }

    public static function draft(): self
    {
        return new self(self::DRAFT);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function is(string $state): bool
    {
        return $this->value === $state;
    }
}
