<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategoryDestinationMediaFallbackReportInterface;
/**
 * Defines the contract for category destination media fallback policy.
 */
interface CategoryDestinationMediaFallbackPolicyInterface
{
    /**
     * @param array<string,mixed> $destinationSettings
     * @param list<mixed>         $bindings
     */
    public function buildReport(string $destinationId, string $categoryId, array $destinationSettings, array $bindings): CategoryDestinationMediaFallbackReportInterface;
}
