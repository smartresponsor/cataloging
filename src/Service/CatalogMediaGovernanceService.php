<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\Catalog\CatalogCategoryMediaBindingEntity;
use App\Cataloging\Event\Catalog\CatalogCategoryMediaBoundEvent;
use App\Cataloging\EventInterface\Catalog\CatalogCategoryMediaBoundEventInterface;
use App\Cataloging\PolicyInterface\CategoryMediaGovernancePolicyInterface;
use App\Cataloging\RepositoryInterface\Catalog\CatalogCategoryMediaBindingRepositoryInterface;
use App\Cataloging\ServiceInterface\CatalogMediaGovernanceServiceInterface;
use App\Cataloging\ValueObject\CategoryMediaBindRequest;
use App\Cataloging\ValueObject\CategoryMediaRole;

/**
 * Provides the catalog media governance service application service.
 */
final readonly class CatalogMediaGovernanceService implements CatalogMediaGovernanceServiceInterface
{
    /**
     * Initializes the catalog media governance service service collaborators.
     */
    public function __construct(
        private CatalogCategoryMediaBindingRepositoryInterface $repository,
        private CategoryMediaGovernancePolicyInterface $policy,
    ) {
    }

    /**
     * Handles the bind workflow.
     */
    public function bind(CategoryMediaBindRequest $request): CatalogCategoryMediaBoundEventInterface
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

        $binding = new CatalogCategoryMediaBindingEntity(
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

        $event = new CatalogCategoryMediaBoundEvent(
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
