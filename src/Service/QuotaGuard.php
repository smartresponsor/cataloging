<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the quota guard application service.
 */
final class QuotaGuard
{
    private int $limit;
    private int $count = 0;
    private int $windowStart;

    /**
     * Initializes the quota guard service collaborators.
     */
    public function __construct(int $limitPerMinute)
    {
        $this->limit = $limitPerMinute;
        $this->windowStart = time();
    }

    /**
     * Handles the allow workflow.
     */
    public function allow(): bool
    {
        $now = time();
        if ($now - $this->windowStart >= 60) {
            $this->windowStart = $now;
            $this->count = 0;
        }
        if ($this->count >= $this->limit) {
            return false;
        }
        ++$this->count;

        return true;
    }
}
