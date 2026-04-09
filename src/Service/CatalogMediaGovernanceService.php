<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Entity\CategoryMediaBinding;
use App\Event\CategoryMediaBound;
use App\PolicyInterface\CategoryMediaGovernancePolicyInterface;
use App\RepositoryInterface\CategoryMediaBindingRepositoryInterface;
use App\ServiceInterface\CatalogMediaGovernanceServiceInterface;
use App\ValueObject\CategoryMediaRole;
/**
 * Provides the catalog media governance service application service.
 */
final class CatalogMediaGovernanceService implements CatalogMediaGovernanceServiceInterface
{
    /**
     * Initializes the catalog media governance service service collaborators.
     */
    public function __construct(
        private readonly CategoryMediaBindingRepositoryInterface $repository,
        private readonly CategoryMediaGovernancePolicyInterface $policy,
    ) {
    }
    /**
     * Handles the bind workflow.
     */
    public function bind(
        string $bindingId,
        string $categoryId,
        string $assetId,
        string $role,
        array $channels,
        array $locales,
        bool $requiredForPublish,
        bool $active,
        array $metadata,
        string $actorId,
        string $reason,
    ): CategoryMediaBound {
        $this->policy->assertBindingAllowed(
            $bindingId,
            $categoryId,
            $assetId,
            $role,
            $channels,
            $locales,
            $actorId,
            $reason,
        );

        $normalizedChannels = array_values(array_unique(array_map(
            static fn ($value): string => trim((string) $value),
            $channels,
        )));
        $normalizedLocales = array_values(array_unique(array_map(
            static fn ($value): string => trim((string) $value),
            $locales,
        )));

        $binding = new CategoryMediaBinding(
            $bindingId,
            $categoryId,
            $assetId,
            CategoryMediaRole::fromString($role),
            $normalizedChannels,
            $normalizedLocales,
            $requiredForPublish,
            $active,
            $metadata,
            $actorId,
            new \DateTimeImmutable('now'),
        );

        $this->repository->save($binding);

        $event = new CategoryMediaBound(
            $bindingId,
            $categoryId,
            $assetId,
            $role,
            $normalizedChannels,
            $normalizedLocales,
            $requiredForPublish,
            $active,
            $metadata,
            $actorId,
            $reason,
            $binding->boundAt(),
        );

        $this->repository->appendHistory($event);

        return $event;
    }
}
