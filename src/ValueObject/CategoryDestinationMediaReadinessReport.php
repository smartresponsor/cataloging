<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategoryDestinationMediaReadinessReportInterface;

/**
 * Represents the category destination media readiness report value.
 */
final readonly class CategoryDestinationMediaReadinessReport implements CategoryDestinationMediaReadinessReportInterface
{
    /**
     * @param array<string,bool> $checks
     * @param list<string>       $requiredMissing
     * @param list<string>       $warnings
     * @param list<string>       $matchedBindingIds
     */
    public function __construct(
        private array $checks,
        private array $requiredMissing,
        private array $warnings,
        private array $matchedBindingIds,
        private bool $publishable,
    ) {
    }

    /**
     * Handles the checks workflow.
     */
    public function checks(): array
    {
        return $this->checks;
    }

    /**
     * Handles the required missing workflow.
     */
    public function requiredMissing(): array
    {
        return $this->requiredMissing;
    }

    /**
     * Handles the warnings workflow.
     */
    public function warnings(): array
    {
        return $this->warnings;
    }

    /**
     * Handles the matched binding ids workflow.
     */
    public function matchedBindingIds(): array
    {
        return $this->matchedBindingIds;
    }

    /**
     * Handles the publishable workflow.
     */
    public function publishable(): bool
    {
        return $this->publishable;
    }
}
