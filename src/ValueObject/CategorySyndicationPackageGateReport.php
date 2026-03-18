<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationPackageGateReportInterface;

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

    public function packageMissingRequiredFields(): array
    {
        return $this->packageMissingRequiredFields;
    }

    public function mediaRequiredMissing(): array
    {
        return $this->mediaRequiredMissing;
    }

    public function warnings(): array
    {
        return $this->warnings;
    }

    public function checks(): array
    {
        return $this->checks;
    }

    public function matchedBindingIds(): array
    {
        return $this->matchedBindingIds;
    }

    public function publishable(): bool
    {
        return $this->publishable;
    }
}
