<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Policy;

use App\Cataloging\PolicyInterface\CategoryMediaGovernancePolicyInterface;
use App\Cataloging\ValueObject\CategoryMediaBindRequest;
use App\Cataloging\ValueObject\CategoryMediaRole;

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
            throw new \InvalidArgumentException('CategoryEntity media bindingId must be provided.');
        }
        if ('' === trim($scope->categoryId())) {
            throw new \InvalidArgumentException('CategoryEntity media categoryId must be provided.');
        }
        if ('' === trim($scope->assetId())) {
            throw new \InvalidArgumentException('CategoryEntity media assetId must be provided.');
        }
        CategoryMediaRole::fromString($scope->role());
        if ([] === $scope->channels()) {
            throw new \InvalidArgumentException('CategoryEntity media channels must contain at least one channel.');
        }
        foreach ($scope->channels() as $channel) {
            if ('' === trim($channel)) {
                throw new \InvalidArgumentException('CategoryEntity media channels must not contain empty values.');
            }
        }
        foreach ($scope->locales() as $locale) {
            if ('' === trim($locale)) {
                throw new \InvalidArgumentException('CategoryEntity media locales must not contain empty values.');
            }
        }
        if ('' === trim($audit->actorId())) {
            throw new \InvalidArgumentException('CategoryEntity media actorId must be provided.');
        }
        if ('' === trim($audit->reason())) {
            throw new \InvalidArgumentException('CategoryEntity media reason must be provided.');
        }
    }
}
