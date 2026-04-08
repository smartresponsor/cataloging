<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationFallbackAwarePackageGateReportInterface;
/**
 * Represents the category syndication fallback aware package gate report value.
 */
final class CategorySyndicationFallbackAwarePackageGateReport implements CategorySyndicationFallbackAwarePackageGateReportInterface
{
    /**
     * @param list<string>       $packageMissingRequiredFields
     * @param list<string>       $strictMediaRequiredMissing
     * @param list<string>       $fallbackMediaRequiredMissing
     * @param list<string>       $warnings
     * @param array<string,bool> $checks
     * @param list<string>       $exactMatchedBindingIds
     * @param list<string>       $fallbackMatchedBindingIds
     */
    public function __construct(
        private readonly array $packageMissingRequiredFields,
        private readonly array $strictMediaRequiredMissing,
        private readonly array $fallbackMediaRequiredMissing,
        private readonly array $warnings,
        private readonly array $checks,
        private readonly array $exactMatchedBindingIds,
        private readonly array $fallbackMatchedBindingIds,
        private readonly bool $strictPublishable,
        private readonly bool $fallbackPublishable,
    ) {
    }
    /**
     * Handles the package missing required fields workflow.
     */
    public function packageMissingRequiredFields(): array
    {
        return $this->packageMissingRequiredFields;
    }
    /**
     * Handles the strict media required missing workflow.
     */
    public function strictMediaRequiredMissing(): array
    {
        return $this->strictMediaRequiredMissing;
    }
    /**
     * Handles the fallback media required missing workflow.
     */
    public function fallbackMediaRequiredMissing(): array
    {
        return $this->fallbackMediaRequiredMissing;
    }
    /**
     * Handles the warnings workflow.
     */
    public function warnings(): array
    {
        return $this->warnings;
    }
    /**
     * Handles the checks workflow.
     */
    public function checks(): array
    {
        return $this->checks;
    }
    /**
     * Handles the exact matched binding ids workflow.
     */
    public function exactMatchedBindingIds(): array
    {
        return $this->exactMatchedBindingIds;
    }
    /**
     * Handles the fallback matched binding ids workflow.
     */
    public function fallbackMatchedBindingIds(): array
    {
        return $this->fallbackMatchedBindingIds;
    }
    /**
     * Handles the strict publishable workflow.
     */
    public function strictPublishable(): bool
    {
        return $this->strictPublishable;
    }
    /**
     * Handles the fallback publishable workflow.
     */
    public function fallbackPublishable(): bool
    {
        return $this->fallbackPublishable;
    }
}
