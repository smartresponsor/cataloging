<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategoryDestinationMediaReadinessReportInterface;
/**
 * Defines the contract for category destination media readiness policy.
 */
interface CategoryDestinationMediaReadinessPolicyInterface
{
    /**
     * @param array<string,mixed> $destinationSettings
     * @param array<string,mixed> $applicabilityPayload
     * @param array<string,bool>  $checks
     * @param list<string>        $requiredMissing
     * @param list<string>        $warnings
     * @param list<string>        $matchedBindingIds
     */
    public function buildReport(
        string $destinationId,
        string $categoryId,
        array $destinationSettings,
        array $applicabilityPayload,
        array $checks,
        array $requiredMissing,
        array $warnings,
        array $matchedBindingIds,
    ): CategoryDestinationMediaReadinessReportInterface;
}
