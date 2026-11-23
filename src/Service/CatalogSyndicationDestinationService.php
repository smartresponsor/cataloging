<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\CategorySyndicationDestination;
use App\Cataloging\Event\CategorySyndicationDestinationRegistered;
use App\Cataloging\EventInterface\CategorySyndicationDestinationRegisteredInterface;
use App\Cataloging\PolicyInterface\CategorySyndicationDestinationPolicyInterface;
use App\Cataloging\RepositoryInterface\CategorySyndicationDestinationRepositoryInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationDestinationServiceInterface;

final class CatalogSyndicationDestinationService implements CatalogSyndicationDestinationServiceInterface
{
    public function __construct(private readonly CategorySyndicationDestinationPolicyInterface $policy, private readonly CategorySyndicationDestinationRepositoryInterface $repository)
    {
    }

    /** @param array<string,mixed> $settings */
    public function register(string $destinationId, string $name, string $destinationType, string $deliveryMode, bool $enabled, array $settings, string $actorId, string $reason): CategorySyndicationDestinationRegisteredInterface
    {
        $this->policy->assertDestinationType($destinationType);
        $this->policy->assertDeliveryMode($deliveryMode);
        /** @var array<string,string> $normalizedSettings */
        $normalizedSettings = $this->policy->normalizeSettings($settings);
        $destination = CategorySyndicationDestination::register($destinationId, $name, $destinationType, $deliveryMode, $enabled, $normalizedSettings, $actorId);
        $this->repository->save($destination);

        return new CategorySyndicationDestinationRegistered(['destinationId' => $destination->destinationId(), 'name' => $destination->name(), 'destinationType' => $destination->destinationType(), 'deliveryMode' => $destination->deliveryMode(), 'enabled' => $destination->enabled(), 'settings' => $destination->settings(), 'actorId' => trim($actorId), 'reason' => trim($reason)], new \DateTimeImmutable('now'));
    }
}
