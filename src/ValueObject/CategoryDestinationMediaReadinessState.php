<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries precomputed readiness state used by destination media policies.
 */
final readonly class CategoryDestinationMediaReadinessState
{
    /**
     * @param array<string,bool> $checks
     * @param list<string>       $requiredMissing
     * @param list<string>       $warnings
     * @param list<string>       $matchedBindingIds
     */
    public function __construct(
        private array $checks,
        private array $requiredMissing,
        private array $warnings,
        private array $matchedBindingIds,
    ) {
    }

    /** @return array<string,bool> */
    public function checks(): array
    {
        return $this->checks;
    }

    /** @return list<string> */
    public function requiredMissing(): array
    {
        return $this->requiredMissing;
    }

    /** @return list<string> */
    public function warnings(): array
    {
        return $this->warnings;
    }

    /** @return list<string> */
    public function matchedBindingIds(): array
    {
        return $this->matchedBindingIds;
    }
}
