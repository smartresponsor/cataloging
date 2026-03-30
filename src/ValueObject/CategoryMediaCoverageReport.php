<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategoryMediaCoverageReportInterface;

final class CategoryMediaCoverageReport implements CategoryMediaCoverageReportInterface
{
    /**
     * @param array<string,bool> $checks
     * @param list<string>       $requiredMissing
     * @param list<string>       $warnings
     */
    public function __construct(
        private readonly array $checks,
        private readonly array $requiredMissing,
        private readonly array $warnings,
    ) {
    }

    public function checks(): array
    {
        return $this->checks;
    }

    public function requiredMissing(): array
    {
        return $this->requiredMissing;
    }

    public function warnings(): array
    {
        return $this->warnings;
    }

    public function mediaReady(): bool
    {
        return ($this->checks['mediaReady'] ?? false) === true;
    }

    public function bannerReady(): bool
    {
        return ($this->checks['bannerReady'] ?? false) === true;
    }

    public function requiredCoverageReady(): bool
    {
        return ($this->checks['requiredMediaCoverageReady'] ?? false) === true;
    }
}
