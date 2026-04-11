<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Entity\CategorySyndicationDestination;
use App\Event\CategorySyndicationDestinationRegistered;
use App\EventInterface\CategorySyndicationDestinationRegisteredInterface;
use App\PolicyInterface\CategorySyndicationDestinationPolicyInterface;
use App\RepositoryInterface\CategorySyndicationDestinationRepositoryInterface;
use App\ServiceInterface\CatalogSyndicationDestinationServiceInterface;
use App\ValueObject\CategorySyndicationDestinationConfiguration;
use App\ValueObject\CategorySyndicationDestinationRegisterRequest;

/**
 * Provides the catalog syndication destination service application service.
 */
final readonly class CatalogSyndicationDestinationService implements CatalogSyndicationDestinationServiceInterface
{
    /**
     * Initializes the catalog syndication destination service service collaborators.
     */
    public function __construct(
        private CategorySyndicationDestinationPolicyInterface $policy,
        private CategorySyndicationDestinationRepositoryInterface $repository,
    ) {
    }

    public function register(
        CategorySyndicationDestinationRegisterRequest $request,
    ): CategorySyndicationDestinationRegisteredInterface {
        $definition = $request->definition();
        $configuration = $request->configuration();
        $audit = $request->auditContext();

        $this->policy->assertDestinationType($definition->destinationType());
        $this->policy->assertDeliveryMode($definition->deliveryMode());

        $normalizedConfiguration = new CategorySyndicationDestinationConfiguration(
            $configuration->enabled(),
            $this->policy->normalizeSettings($configuration->settings()),
        );

        $destination = CategorySyndicationDestination::register(
            $definition,
            $normalizedConfiguration,
            $audit->actorId(),
        );
        $this->repository->save($destination);

        return new CategorySyndicationDestinationRegistered(
            [
                'destinationId' => $destination->destinationId(),
                'name' => $destination->name(),
                'destinationType' => $destination->destinationType(),
                'deliveryMode' => $destination->deliveryMode(),
                'enabled' => $destination->enabled(),
                'settings' => $destination->settings(),
                'actorId' => trim($audit->actorId()),
                'reason' => trim($audit->reason()),
            ],
            new \DateTimeImmutable('now'),
        );
    }
}
