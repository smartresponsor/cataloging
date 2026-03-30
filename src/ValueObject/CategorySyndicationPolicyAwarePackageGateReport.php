<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationPolicyAwarePackageGateReportInterface;

final class CategorySyndicationPolicyAwarePackageGateReport implements CategorySyndicationPolicyAwarePackageGateReportInterface
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
        private readonly string $mediaPolicyMode,
        private readonly array $packageMissingRequiredFields,
        private readonly array $requiredMissing,
        private readonly array $warnings,
        private readonly array $checks,
        private readonly array $exactMatchedBindingIds,
        private readonly array $fallbackMatchedBindingIds,
        private readonly bool $strictPublishable,
        private readonly bool $fallbackPublishable,
        private readonly bool $resolvedPublishable,
        private readonly bool $fallbackUsed,
    ) {
    }

    public function mediaPolicyMode(): string
    {
        return $this->mediaPolicyMode;
    }

    public function packageMissingRequiredFields(): array
    {
        return $this->packageMissingRequiredFields;
    }

    public function requiredMissing(): array
    {
        return $this->requiredMissing;
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

    public function resolvedPublishable(): bool
    {
        return $this->resolvedPublishable;
    }

    public function fallbackUsed(): bool
    {
        return $this->fallbackUsed;
    }
}
