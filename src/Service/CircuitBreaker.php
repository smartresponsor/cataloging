<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

/**
 * Provides the circuit breaker application service.
 */
final class CircuitBreaker
{
    private int $failCount = 0;
    private bool $open = false;

    /**
     * Handles the record success workflow.
     */
    public function recordSuccess(): void
    {
        $this->failCount = 0;
        $this->open = false;
    }

    /**
     * Handles the record failure workflow.
     */
    public function recordFailure(): void
    {
        ++$this->failCount;
        if ($this->failCount >= 3) {
            $this->open = true;
        }
    }

    /**
     * Determines whether the open condition is satisfied.
     */
    public function isOpen(): bool
    {
        return $this->open;
    }
}
