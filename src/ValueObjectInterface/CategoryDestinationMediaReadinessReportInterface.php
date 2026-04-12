<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObjectInterface;

/**
 * Defines the contract for category destination media readiness report.
 */
interface CategoryDestinationMediaReadinessReportInterface
{
    /** @return array<string,bool> */
    public function checks(): array;

    /** @return list<string> */
    public function requiredMissing(): array;

    /** @return list<string> */
    public function warnings(): array;

    /** @return list<string> */
    public function matchedBindingIds(): array;

    /**
     * Handles the publishable workflow.
     */
    public function publishable(): bool;
}
