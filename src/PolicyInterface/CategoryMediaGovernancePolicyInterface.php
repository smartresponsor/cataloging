<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\PolicyInterface;

use App\Cataloging\ValueObject\CategoryMediaBindRequest;

/**
 * Defines the contract for category media governance policy.
 */
interface CategoryMediaGovernancePolicyInterface
{
    public function assertBindingAllowed(CategoryMediaBindRequest $request): void;
}
