<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Event;

use App\EventInterface\CategoryMovedInterface;

final class CategoryMoved implements CategoryMovedInterface
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
