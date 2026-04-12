<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\EventInterface;

/**
 * Defines the contract for category change request assigned.
 */
interface CategoryChangeRequestAssignedInterface
{
    /** @return array<string,mixed> */
    public function payload(): array;
}
