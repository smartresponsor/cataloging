<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service;

final class QuotaGuard
{
    private int $limit;
    private int $count = 0;
    private int $windowStart;

    public function __construct(int $limitPerMinute)
    {
        $this->limit = $limitPerMinute;
        $this->windowStart = time();
    }

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
