<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationPackageGateReportInterface;
/**
 * Represents the category syndication package gate report value.
 */
final class CategorySyndicationPackageGateReport implements CategorySyndicationPackageGateReportInterface
{
    /**
     * @param list<string>       $packageMissingRequiredFields
     * @param list<string>       $mediaRequiredMissing
     * @param list<string>       $warnings
     * @param array<string,bool> $checks
     * @param list<string>       $matchedBindingIds
     */
    public function __construct(
        private readonly array $packageMissingRequiredFields,
        private readonly array $mediaRequiredMissing,
        private readonly array $warnings,
        private readonly array $checks,
        private readonly array $matchedBindingIds,
        private readonly bool $publishable,
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
     * Handles the media required missing workflow.
     */
    public function mediaRequiredMissing(): array
    {
        return $this->mediaRequiredMissing;
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
