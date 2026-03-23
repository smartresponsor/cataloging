<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategorySyndicationDestinationRegisteredInterface;

interface CategorySyndicationDestinationServiceInterface
{
    /**
     * @param array<string,string> $settings
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
