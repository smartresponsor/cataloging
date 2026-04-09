<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategoryMediaCoverageReportInterface;

/**
 * Represents the category media coverage report value.
 */
final readonly class CategoryMediaCoverageReport implements CategoryMediaCoverageReportInterface
{
    /**
     * @param array<string,bool> $checks
     * @param list<string>       $requiredMissing
     * @param list<string>       $warnings
     */
    public function __construct(
        private array $checks,
        private array $requiredMissing,
        private array $warnings,
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
     * Handles the media ready workflow.
     */
    public function mediaReady(): bool
    {
        return ($this->checks['mediaReady'] ?? false) === true;
    }

    /**
     * Handles the banner ready workflow.
     */
    public function bannerReady(): bool
    {
        return ($this->checks['bannerReady'] ?? false) === true;
    }

    /**
     * Handles the required coverage ready workflow.
     */
    public function requiredCoverageReady(): bool
    {
        return ($this->checks['requiredMediaCoverageReady'] ?? false) === true;
    }
}
