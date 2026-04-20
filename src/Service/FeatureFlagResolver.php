<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the feature flag resolver application service.
 */
final readonly class FeatureFlagResolver
{
    /** @param array<string,bool> $flags */
    public function __construct(private array $flags = [])
    {
    }

    /**
     * Determines whether the enabled condition is satisfied.
     */
    public function isEnabled(string $flag): bool
    {
        return $this->flags[$flag] ?? false;
    }
}
