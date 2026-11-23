<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\PolicyInterface;

use App\Cataloging\ValueObjectInterface\CategoryMediaCoverageReportInterface;

interface CategoryMediaCoveragePolicyInterface
{
    /**
     * @param array<string,mixed>                                      $payload
     * @param list<\App\Cataloging\EntityInterface\CategoryMediaBindingInterface> $bindings
     */
    public function buildReport(array $payload, array $bindings): CategoryMediaCoverageReportInterface;
}
