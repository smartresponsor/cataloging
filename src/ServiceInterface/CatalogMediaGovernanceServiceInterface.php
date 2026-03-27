<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategoryMediaBoundInterface;

interface CatalogMediaGovernanceServiceInterface
{
    /**
     * @param list<string>        $channels
     * @param list<string>        $locales
     * @param array<string,mixed> $metadata
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
    ): CategoryMediaBoundInterface;
}
