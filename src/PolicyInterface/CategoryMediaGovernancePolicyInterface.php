<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

use App\ValueObject\CategoryMediaBindRequest;

/**
 * Defines the contract for category media governance policy.
 */
interface CategoryMediaGovernancePolicyInterface
{
    public function assertBindingAllowed(CategoryMediaBindRequest $request): void;
}
