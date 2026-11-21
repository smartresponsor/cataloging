<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries category quota allowance evaluation input.
 */
final readonly class CategoryQuotaAllowanceRequest
{
    public function __construct(
        private string $scope,
        private string $id,
        private string $operation,
        private int $capacity,
        private float $ratePerSecond,
    ) {
    }

    public function scope(): string
    {
        return trim($this->scope);
    }

    public function id(): string
    {
        return trim($this->id);
    }

    public function operation(): string
    {
        return trim($this->operation);
    }

    public function capacity(): int
    {
        return $this->capacity;
    }

    public function ratePerSecond(): float
    {
        return $this->ratePerSecond;
    }
}
