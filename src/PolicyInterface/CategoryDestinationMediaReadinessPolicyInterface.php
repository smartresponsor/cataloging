<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategoryDestinationMediaReadinessReportInterface;

interface CategoryDestinationMediaReadinessPolicyInterface
{
    /**
     * @param array<string,mixed> $destinationSettings
     */
    public function buildReport(string $destinationId, string $categoryId, array $destinationSettings, array $applicabilityPayload, array $checks, array $requiredMissing, array $warnings, array $matchedBindingIds): CategoryDestinationMediaReadinessReportInterface;
}
