<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationFallbackAwarePackageGateReportInterface;

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

    public function packageMissingRequiredFields(): array
    {
        return $this->packageMissingRequiredFields;
    }

    public function strictMediaRequiredMissing(): array
    {
        return $this->strictMediaRequiredMissing;
    }

    public function fallbackMediaRequiredMissing(): array
    {
        return $this->fallbackMediaRequiredMissing;
    }

    public function warnings(): array
    {
        return $this->warnings;
    }

    public function checks(): array
    {
        return $this->checks;
    }

    public function exactMatchedBindingIds(): array
    {
        return $this->exactMatchedBindingIds;
    }

    public function fallbackMatchedBindingIds(): array
    {
        return $this->fallbackMatchedBindingIds;
    }

    public function strictPublishable(): bool
    {
        return $this->strictPublishable;
    }

    public function fallbackPublishable(): bool
    {
        return $this->fallbackPublishable;
    }
}
