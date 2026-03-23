<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Entity\CategorySyndicationDestination;
use App\Event\CategorySyndicationDestinationRegistered;
use App\EventInterface\CategorySyndicationDestinationRegisteredInterface;
use App\PolicyInterface\CategorySyndicationDestinationPolicyInterface;
use App\RepositoryInterface\CategorySyndicationDestinationRepositoryInterface;
use App\ServiceInterface\CategorySyndicationDestinationServiceInterface;

final class CategorySyndicationDestinationService implements CategorySyndicationDestinationServiceInterface
{
    public function __construct(
        private readonly CategorySyndicationDestinationPolicyInterface $policy,
        private readonly CategorySyndicationDestinationRepositoryInterface $repository,
    ) {
    }

    public function register(
        string $destinationId,
        string $name,
        string $destinationType,
        string $deliveryMode,
        bool $enabled,
        array $settings,
        string $actorId,
        string $reason,
    ): CategorySyndicationDestinationRegisteredInterface {
        $this->policy->assertDestinationType($destinationType);
        $this->policy->assertDeliveryMode($deliveryMode);
        $normalizedSettings = $this->policy->normalizeSettings($settings);

        $destination = CategorySyndicationDestination::register(
            $destinationId,
            $name,
            $destinationType,
            $deliveryMode,
            $enabled,
            $normalizedSettings,
            $actorId,
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
                'actorId' => trim($actorId),
                'reason' => trim($reason),
            ],
            new \DateTimeImmutable('now'),
        );
    }
}
