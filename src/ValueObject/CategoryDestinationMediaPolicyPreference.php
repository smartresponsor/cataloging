<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategoryDestinationMediaPolicyPreferenceInterface;

/**
 * Represents the category destination media policy preference value.
 */
final readonly class CategoryDestinationMediaPolicyPreference implements CategoryDestinationMediaPolicyPreferenceInterface
{
    /**
     * @param array<string,bool> $checks
     * @param list<string>       $requiredMissing
     * @param list<string>       $warnings
     */
    public function __construct(
        private string $mediaPolicyMode,
        private array $checks,
        private array $requiredMissing,
        private array $warnings,
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
