<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategoryDestinationMediaReadinessReportInterface;

final class CategoryDestinationMediaReadinessReport implements CategoryDestinationMediaReadinessReportInterface
{
    /**
     * @param array<string,bool> $checks
     * @param list<string>       $requiredMissing
     * @param list<string>       $warnings
     * @param list<string>       $matchedBindingIds
     */
    public function __construct(
        private readonly array $checks,
        private readonly array $requiredMissing,
        private readonly array $warnings,
        private readonly array $matchedBindingIds,
        private readonly bool $publishable,
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

    public function matchedBindingIds(): array
    {
        return $this->matchedBindingIds;
    }

    public function publishable(): bool
    {
        return $this->publishable;
    }
}
