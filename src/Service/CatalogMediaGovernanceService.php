<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Entity\CategoryMediaBinding;
use App\Event\CategoryMediaBound;
use App\PolicyInterface\CategoryMediaGovernancePolicyInterface;
use App\RepositoryInterface\CategoryMediaBindingRepositoryInterface;
use App\ServiceInterface\CatalogMediaGovernanceServiceInterface;
use App\ValueObject\CategoryMediaBindRequest;
use App\ValueObject\CategoryMediaRole;

/**
 * Provides the catalog media governance service application service.
 */
final readonly class CatalogMediaGovernanceService implements CatalogMediaGovernanceServiceInterface
{
    /**
     * Initializes the catalog media governance service service collaborators.
     */
    public function __construct(
        private CategoryMediaBindingRepositoryInterface $repository,
        private CategoryMediaGovernancePolicyInterface $policy,
    ) {
    }

    /**
     * Handles the bind workflow.
     */
    public function bind(CategoryMediaBindRequest $request): CategoryMediaBound
    {
        $scope = $request->scope();
        $state = $request->state();
        $audit = $request->auditContext();

        $this->policy->assertBindingAllowed($request);

        $normalizedChannels = array_values(array_unique(array_map(
            static fn ($value): string => trim((string) $value),
            $scope->channels(),
        )));
        $normalizedLocales = array_values(array_unique(array_map(
            static fn ($value): string => trim((string) $value),
            $scope->locales(),
        )));

        $binding = new CategoryMediaBinding(
            $scope->bindingId(),
            $scope->categoryId(),
            $scope->assetId(),
            CategoryMediaRole::fromString($scope->role()),
            $normalizedChannels,
            $normalizedLocales,
            $state->requiredForPublish(),
            $state->active(),
            $state->metadata(),
            $audit->actorId(),
            new \DateTimeImmutable('now'),
        );

        $this->repository->save($binding);

        $event = new CategoryMediaBound(
            $scope->bindingId(),
            $scope->categoryId(),
            $scope->assetId(),
            $scope->role(),
            $normalizedChannels,
            $normalizedLocales,
            $state->requiredForPublish(),
            $state->active(),
            $state->metadata(),
            $audit->actorId(),
            $audit->reason(),
            $binding->boundAt(),
        );

        $this->repository->appendHistory($event);

        return $event;
    }
}
