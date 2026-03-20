<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategoryMediaCoverageReportInterface;

interface CategoryMediaCoveragePolicyInterface
{
    /**
     * @param array<string,mixed>                                      $payload
     * @param list<\App\EntityInterface\CategoryMediaBindingInterface> $bindings
     */
    public function buildReport(array $payload, array $bindings): CategoryMediaCoverageReportInterface;
}
