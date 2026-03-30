<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\PolicyInterface\CategoryMediaGovernancePolicyInterface;
use App\ValueObject\CategoryMediaRole;

final class CategoryMediaGovernancePolicy implements CategoryMediaGovernancePolicyInterface
{
    /**
     * @param list<string|int|float|bool|null> $channels
     * @param list<string|int|float|bool|null> $locales
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
            if ('' === trim((string) ($channel ?? ''))) {
                throw new \InvalidArgumentException('Category media channels must not contain empty values.');
            }
        }
        foreach ($locales as $locale) {
            if ('' === trim((string) ($locale ?? ''))) {
                throw new \InvalidArgumentException('Category media locales must not contain empty values.');
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
