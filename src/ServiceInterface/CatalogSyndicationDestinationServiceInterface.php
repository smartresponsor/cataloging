<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategorySyndicationDestinationRegisteredInterface;
/**
 * Defines the contract for catalog syndication destination service.
 */
interface CatalogSyndicationDestinationServiceInterface
{
    /**
     * @param array<string,mixed> $settings
     */
    public function register(
        string $destinationId,
        string $name,
        string $destinationType,
        string $deliveryMode,
        bool $enabled,
        array $settings,
        string $actorId,
        string $reason,
    ): CategorySyndicationDestinationRegisteredInterface;
}
