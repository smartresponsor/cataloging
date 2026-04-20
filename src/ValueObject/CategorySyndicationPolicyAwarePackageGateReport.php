<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

use App\Cataloging\ValueObjectInterface\CategorySyndicationPolicyAwarePackageGateReportInterface;

/**
 * Represents the category syndication policy aware package gate report value.
 */
final readonly class CategorySyndicationPolicyAwarePackageGateReport implements CategorySyndicationPolicyAwarePackageGateReportInterface
{
    /**
     * @param list<string>       $packageMissingRequiredFields
     * @param list<string>       $requiredMissing
     * @param list<string>       $warnings
     * @param array<string,bool> $checks
     * @param list<string>       $exactMatchedBindingIds
     * @param list<string>       $fallbackMatchedBindingIds
     */
    public function __construct(
        private string $mediaPolicyMode,
        private array $packageMissingRequiredFields,
        private array $requiredMissing,
        private array $warnings,
        private array $checks,
        private array $exactMatchedBindingIds,
        private array $fallbackMatchedBindingIds,
        private bool $strictPublishable,
        private bool $fallbackPublishable,
        private bool $resolvedPublishable,
        private bool $fallbackUsed,
    ) {
    }

    /**
     * Handles the media policy mode workflow.
     */
    public function mediaPolicyMode(): string
    {
        return $this->mediaPolicyMode;
    }

    /**
     * Handles the package missing required fields workflow.
     */
    public function packageMissingRequiredFields(): array
    {
        return $this->packageMissingRequiredFields;
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

    /**
     * Resolves the d publishable result for the current workflow.
     */
    public function resolvedPublishable(): bool
    {
        return $this->resolvedPublishable;
    }

    /**
     * Handles the fallback used workflow.
     */
    public function fallbackUsed(): bool
    {
        return $this->fallbackUsed;
    }
}
