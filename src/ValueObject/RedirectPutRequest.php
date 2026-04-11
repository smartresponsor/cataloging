<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries the normalized redirect write surface.
 */
final readonly class RedirectPutRequest
{
    public function __construct(
        private string $from,
        private string $to,
        private int $status = 301,
    ) {
    }

    public function from(): string
    {
        return $this->from;
    }

    public function to(): string
    {
        return $this->to;
    }

    public function status(): int
    {
        return $this->status;
    }
}
