<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;
/**
 * Defines the contract for category media governance policy.
 */
interface CategoryMediaGovernancePolicyInterface
{
    /**
     * @param list<string> $channels
     * @param list<string> $locales
     */
    public function assertBindingAllowed(
        string $bindingId,
        string $categoryId,
        string $assetId,
        string $role,
        array $channels,
        array $locales,
        string $actorId,
        string $reason,
    ): void;
}
