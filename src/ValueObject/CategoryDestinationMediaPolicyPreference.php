<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\ValueObject;

use App\ValueObjectInterface\CategoryDestinationMediaPolicyPreferenceInterface;

final class CategoryDestinationMediaPolicyPreference implements CategoryDestinationMediaPolicyPreferenceInterface
{
    /**
     * @param array<string,bool> $checks
     * @param list<string>       $requiredMissing
     * @param list<string>       $warnings
     */
    public function __construct(
        private readonly string $mediaPolicyMode,
        private readonly array $checks,
        private readonly array $requiredMissing,
        private readonly array $warnings,
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
