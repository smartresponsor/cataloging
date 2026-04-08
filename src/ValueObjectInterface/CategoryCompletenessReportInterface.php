<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObjectInterface;
/**
 * Defines the contract for category completeness report.
 */
interface CategoryCompletenessReportInterface
{
    /**
     * Handles the score workflow.
     */
    public function score(): int;
    /**
     * Determines whether the complete condition is satisfied.
     */
    public function isComplete(): bool;

    /** @return list<string> */
    public function missingRequired(): array;

    /** @return list<string> */
    public function warnings(): array;

    /** @return array<string,bool> */
    public function checks(): array;

    /** @return array<string,bool> */
    public function publicationChecks(): array;
}
