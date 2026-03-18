<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\ValueObject;

use App\ValueObjectInterface\CategoryDestinationMediaFallbackReportInterface;

final class CategoryDestinationMediaFallbackReport implements CategoryDestinationMediaFallbackReportInterface
{
    /**
     * @param array<string,bool> $checks
     * @param list<string>       $requiredMissing
     * @param list<string>       $warnings
     * @param list<string>       $exactMatchedBindingIds
     * @param list<string>       $fallbackMatchedBindingIds
     */
    public function __construct(
        private readonly array $checks,
        private readonly array $requiredMissing,
        private readonly array $warnings,
        private readonly array $exactMatchedBindingIds,
        private readonly array $fallbackMatchedBindingIds,
        private readonly bool $publishable,
        private readonly bool $publishableWithFallback,
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

    public function exactMatchedBindingIds(): array
    {
        return $this->exactMatchedBindingIds;
    }

    public function fallbackMatchedBindingIds(): array
    {
        return $this->fallbackMatchedBindingIds;
    }

    public function publishable(): bool
    {
        return $this->publishable;
    }

    public function publishableWithFallback(): bool
    {
        return $this->publishableWithFallback;
    }
}
