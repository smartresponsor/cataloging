<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategoryChangeRequestStateInterface;

/**
 * Represents the category change request state value.
 */
final class CategoryChangeRequestState implements CategoryChangeRequestStateInterface
{
    public const string PROPOSED = 'proposed';
    public const string IN_REVIEW = 'in_review';
    public const string ACCEPTED = 'accepted';
    public const string REJECTED = 'rejected';
    public const string WITHDRAWN = 'withdrawn';

    private function __construct(
        private readonly string $value,
    ) {
    }

    public static function fromString(string $value): self
    {
        $normalized = strtolower(trim($value));
        $allowed = [
            self::PROPOSED,
            self::IN_REVIEW,
            self::ACCEPTED,
            self::REJECTED,
            self::WITHDRAWN,
        ];

        if (!in_array($normalized, $allowed, true)) {
            throw new \InvalidArgumentException(sprintf('Unsupported category change request state: %s', $value));
        }

        return new self($normalized);
    }

    public static function proposed(): self
    {
        return new self(self::PROPOSED);
    }

    public static function inReview(): self
    {
        return new self(self::IN_REVIEW);
    }

    public static function accepted(): self
    {
        return new self(self::ACCEPTED);
    }

    public static function rejected(): self
    {
        return new self(self::REJECTED);
    }

    public static function withdrawn(): self
    {
        return new self(self::WITHDRAWN);
    }

    /**
     * Handles the value workflow.
     */
    public function value(): string
    {
        return $this->value;
    }
}
