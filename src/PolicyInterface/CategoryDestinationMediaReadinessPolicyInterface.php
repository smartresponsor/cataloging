<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

use App\ValueObject\CategoryDestinationMediaReadinessContext;
use App\ValueObject\CategoryDestinationMediaReadinessState;
use App\ValueObjectInterface\CategoryDestinationMediaReadinessReportInterface;

/**
 * Defines the contract for category destination media readiness policy.
 */
interface CategoryDestinationMediaReadinessPolicyInterface
{
    public function buildReport(
        CategoryDestinationMediaReadinessContext $context,
        CategoryDestinationMediaReadinessState $state,
    ): CategoryDestinationMediaReadinessReportInterface;
}
