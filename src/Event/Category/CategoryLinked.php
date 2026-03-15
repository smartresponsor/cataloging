<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Event\Category;

/** Emitted after link attach. */
final class CategoryLinked
{
    /** @var array<string,mixed> */
    private array $payload;

    /** @param array<string,mixed> $payload */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /** @return array<string,mixed> */
    public function payload(): array
    {
        return $this->payload;
    }
}
