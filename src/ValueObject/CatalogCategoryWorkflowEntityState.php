<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

use App\Cataloging\ValueObjectInterface\CatalogCategoryWorkflowEntityStateInterface;

/**
 * Represents the category workflow state value object.
 */
final class CatalogCategoryWorkflowEntityState implements CatalogCategoryWorkflowEntityStateInterface
{
    public const DRAFT = 'draft';
    public const IN_REVIEW = 'in_review';
    public const APPROVED = 'approved';
    public const PUBLISHED = 'published';
    public const ARCHIVED = 'archived';

    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        $normalized = strtolower(trim($value));

        return match ($normalized) {
            self::DRAFT => new self(self::DRAFT),
            self::IN_REVIEW => new self(self::IN_REVIEW),
            self::APPROVED => new self(self::APPROVED),
            self::PUBLISHED => new self(self::PUBLISHED),
            self::ARCHIVED => new self(self::ARCHIVED),
            default => throw new \InvalidArgumentException(sprintf('Unsupported category workflow state "%s".', $value)),
        };
    }

    public static function draft(): self
    {
        return new self(self::DRAFT);
    }

    public static function inReview(): self
    {
        return new self(self::IN_REVIEW);
    }

    public static function approved(): self
    {
        return new self(self::APPROVED);
    }

    public static function published(): self
    {
        return new self(self::PUBLISHED);
    }

    public static function archived(): self
    {
        return new self(self::ARCHIVED);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(CatalogCategoryWorkflowEntityStateInterface $other): bool
    {
        return $this->value === $other->value();
    }

    /**
     * @param string|CatalogCategoryWorkflowEntityStateInterface $expected
     */
    public function is(string|CatalogCategoryWorkflowEntityStateInterface $expected): bool
    {
        $expectedValue = $expected instanceof CatalogCategoryWorkflowEntityStateInterface
            ? $expected->value()
            : strtolower(trim($expected));

        return $this->value === $expectedValue;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
