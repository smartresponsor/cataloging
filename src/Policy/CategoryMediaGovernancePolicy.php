<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\PolicyInterface\CategoryMediaGovernancePolicyInterface;
use App\ValueObject\CategoryMediaBindRequest;
use App\ValueObject\CategoryMediaRole;

/**
 * Provides the category media governance policy implementation.
 */
/** @noinspection PhpCastIsUnnecessaryInspection */
final class CategoryMediaGovernancePolicy implements CategoryMediaGovernancePolicyInterface
{
    public function assertBindingAllowed(CategoryMediaBindRequest $request): void
    {
        $scope = $request->scope();
        $audit = $request->auditContext();

        if ('' === trim($scope->bindingId())) {
            throw new \InvalidArgumentException('Category media bindingId must be provided.');
        }
        if ('' === trim($scope->categoryId())) {
            throw new \InvalidArgumentException('Category media categoryId must be provided.');
        }
        if ('' === trim($scope->assetId())) {
            throw new \InvalidArgumentException('Category media assetId must be provided.');
        }
        CategoryMediaRole::fromString($scope->role());
        if ([] === $scope->channels()) {
            throw new \InvalidArgumentException('Category media channels must contain at least one channel.');
        }
        foreach ($scope->channels() as $channel) {
            if ('' === trim($channel)) {
                throw new \InvalidArgumentException('Category media channels must not contain empty values.');
            }
        }
        foreach ($scope->locales() as $locale) {
            if ('' === trim($locale)) {
                throw new \InvalidArgumentException('Category media locales must not contain empty values.');
            }
        }
        if ('' === trim($audit->actorId())) {
            throw new \InvalidArgumentException('Category media actorId must be provided.');
        }
        if ('' === trim($audit->reason())) {
            throw new \InvalidArgumentException('Category media reason must be provided.');
        }
    }
}
