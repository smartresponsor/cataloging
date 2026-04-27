<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject\Role;

/**
 * Represents the subject id domain record.
 */
final readonly class SubjectId
{
    /**
     * Initializes the subject id service collaborators.
     */
    public function __construct(private mixed $value)
    {
    }

    /**
     * Handles the value workflow.
     */
    public function value(): mixed
    {
        return $this->value;
    }

    /**
     * Handles the to string workflow.
     */
    public function __toString(): string
    {
        return is_scalar($this->value) || $this->value instanceof \Stringable ? (string) $this->value : '';
    }
}
