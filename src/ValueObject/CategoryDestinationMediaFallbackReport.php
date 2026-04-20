<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

use App\Cataloging\ValueObjectInterface\CategoryDestinationMediaFallbackReportInterface;

/**
 * Represents the category destination media fallback report value.
 */
final readonly class CategoryDestinationMediaFallbackReport implements CategoryDestinationMediaFallbackReportInterface
{
    /**
     * @param array<string,bool> $checks
     * @param list<string>       $requiredMissing
     * @param list<string>       $warnings
     * @param list<string>       $exactMatchedBindingIds
     * @param list<string>       $fallbackMatchedBindingIds
     */
    public function __construct(
        private array $checks,
        private array $requiredMissing,
        private array $warnings,
        private array $exactMatchedBindingIds,
        private array $fallbackMatchedBindingIds,
        private bool $publishable,
        private bool $publishableWithFallback,
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
     * Handles the publishable workflow.
     */
    public function publishable(): bool
    {
        return $this->publishable;
    }

    /**
     * Handles the publishable with fallback workflow.
     */
    public function publishableWithFallback(): bool
    {
        return $this->publishableWithFallback;
    }
}
