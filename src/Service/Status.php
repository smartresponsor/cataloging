<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the status application service.
 */
final class Status
{
    public const string DRAFT = 'draft';
    public const string PUBLISHED = 'published';
    private string $value;

    /**
     * Initializes the status service collaborators.
     */
    public function __construct(string $value)
    {
        if (!in_array($value, [self::DRAFT, self::PUBLISHED], true)) {
            throw new \InvalidArgumentException('Invalid status');
        }
        $this->value = $value;
    }

    /**
     * Handles the value workflow.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Determines whether the draft condition is satisfied.
     */
    public function isDraft(): bool
    {
        return self::DRAFT === $this->value;
    }

    /**
     * Determines whether the published condition is satisfied.
     */
    public function isPublished(): bool
    {
        return self::PUBLISHED === $this->value;
    }
}
