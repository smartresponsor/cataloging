<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\PolicyInterface;

use App\Cataloging\EntityInterface\CatalogCategoryMediaBindingEntityInterface;
use App\Cataloging\ValueObjectInterface\CategoryMediaApplicabilityReportInterface;

/**
 * Defines the contract for category media applicability policy.
 */
interface CategoryMediaApplicabilityPolicyInterface
{
    /**
     * @param array<string,mixed>                              $payload
     * @param list<CatalogCategoryMediaBindingEntityInterface> $bindings
     */
    public function buildReport(array $payload, array $bindings): CategoryMediaApplicabilityReportInterface;
}
