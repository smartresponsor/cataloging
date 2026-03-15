<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>.
 */
declare(strict_types=1);

namespace Layer\Category;

final class CircuitBreaker
{
    private int $failCount = 0;
    private bool $open = false;

    public function recordSuccess(): void
    {
        $this->failCount = 0;
        $this->open = false;
    }

    public function recordFailure(): void
    {
        ++$this->failCount;
        if ($this->failCount >= 3) {
            $this->open = true;
        }
    }

    public function isOpen(): bool
    {
        return $this->open;
    }
}
