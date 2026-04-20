<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\PolicyInterface;

use App\Cataloging\ValueObject\CategoryDestinationMediaReadinessContext;
use App\Cataloging\ValueObject\CategoryDestinationMediaReadinessState;
use App\Cataloging\ValueObjectInterface\CategoryDestinationMediaReadinessReportInterface;

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
