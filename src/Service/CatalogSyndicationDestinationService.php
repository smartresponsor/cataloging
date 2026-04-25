<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\CatalogSyndicationDestinationEntity;
use App\Cataloging\Event\CatalogSyndicationDestinationRegistered;
use App\Cataloging\EventInterface\CatalogSyndicationDestinationRegisteredInterface;
use App\Cataloging\PolicyInterface\CatalogSyndicationDestinationPolicyInterface;
use App\Cataloging\RepositoryInterface\CatalogSyndicationDestinationRepositoryInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationDestinationServiceInterface;
use App\Cataloging\ValueObject\CatalogSyndicationDestinationConfiguration;
use App\Cataloging\ValueObject\CatalogSyndicationDestinationRegisterRequest;

/**
 * Provides the catalog syndication destination service application service.
 */
final readonly class CatalogSyndicationDestinationService implements CatalogSyndicationDestinationServiceInterface
{
    /**
     * Initializes the catalog syndication destination service service collaborators.
     */
    public function __construct(
        private CatalogSyndicationDestinationPolicyInterface $policy,
        private CatalogSyndicationDestinationRepositoryInterface $repository,
    ) {
    }

    public function register(
        CatalogSyndicationDestinationRegisterRequest $request,
    ): CatalogSyndicationDestinationRegisteredInterface {
        $definition = $request->definition();
        $configuration = $request->configuration();
        $audit = $request->auditContext();

        $this->policy->assertDestinationType($definition->destinationType());
        $this->policy->assertDeliveryMode($definition->deliveryMode());

        $normalizedConfiguration = new CatalogSyndicationDestinationConfiguration(
            $configuration->enabled(),
            $this->policy->normalizeSettings($configuration->settings()),
        );

        $destination = CatalogSyndicationDestinationEntity::register(
            $definition,
            $normalizedConfiguration,
            $audit->actorId(),
        );
        $this->repository->save($destination);

        return new CatalogSyndicationDestinationRegistered(
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
