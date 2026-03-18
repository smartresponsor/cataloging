<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Policy;

use App\PolicyInterface\CategoryMediaGovernancePolicyInterface;
use App\ValueObject\CategoryMediaRole;

final class CategoryMediaGovernancePolicy implements CategoryMediaGovernancePolicyInterface
{
    public function assertBindingAllowed(
        string $bindingId,
        string $categoryId,
        string $assetId,
        string $role,
        array $channels,
        array $locales,
        string $actorId,
        string $reason,
    ): void {
        if ('' === trim($bindingId)) {
            throw new \InvalidArgumentException('Category media bindingId must be provided.');
        }
        if ('' === trim($categoryId)) {
            throw new \InvalidArgumentException('Category media categoryId must be provided.');
        }
        if ('' === trim($assetId)) {
            throw new \InvalidArgumentException('Category media assetId must be provided.');
        }
        CategoryMediaRole::fromString($role);
        if ([] === $channels) {
            throw new \InvalidArgumentException('Category media channels must contain at least one channel.');
        }
        foreach ($channels as $channel) {
            if (!is_scalar($channel) && null !== $channel) {
                throw new \InvalidArgumentException('Category media channels must contain only scalar values.');
            }
        }
        foreach ($locales as $locale) {
            if (!is_scalar($locale) && null !== $locale) {
                throw new \InvalidArgumentException('Category media locales must contain only scalar values.');
            }
        }
        if ('' === trim($actorId)) {
            throw new \InvalidArgumentException('Category media actorId must be provided.');
        }
        if ('' === trim($reason)) {
            throw new \InvalidArgumentException('Category media reason must be provided.');
        }
    }
}
