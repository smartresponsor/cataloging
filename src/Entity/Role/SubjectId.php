<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity\Role;

final class SubjectId
{
    public function __construct(private readonly mixed $value)
    {
    }

    public function value(): mixed
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return is_scalar($this->value) || $this->value instanceof \Stringable ? (string) $this->value : '';
    }
}
