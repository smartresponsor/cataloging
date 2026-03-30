<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

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
