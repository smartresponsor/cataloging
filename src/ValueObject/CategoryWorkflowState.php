<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

use App\Cataloging\ValueObjectInterface\CategoryWorkflowStateInterface;

/**
 * Represents the category workflow state value.
 */
final class CategoryWorkflowState implements CategoryWorkflowStateInterface
{
    public const string DRAFT = 'draft';
    public const string IN_REVIEW = 'in_review';
    public const string APPROVED = 'approved';
    public const string PUBLISHED = 'published';
    public const string ARCHIVED = 'archived';
    private const array ALLOWED = [
        self::DRAFT,
        self::IN_REVIEW,
        self::APPROVED,
        self::PUBLISHED,
        self::ARCHIVED,
    ];

    /**
     * Initializes the category workflow state service collaborators.
     */
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

    /**
     * Handles the value workflow.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Handles the is workflow.
     */
    public function is(string $state): bool
    {
        return $this->value === $state;
    }
}
