<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategoryDestinationMediaReadinessEvaluatedInterface;
/**
 * Defines the contract for catalog destination media readiness service.
 */
interface CatalogDestinationMediaReadinessServiceInterface
{
    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(
        string $destinationId,
        string $categoryId,
        string $actorId,
        string $reason,
    ): CategoryDestinationMediaReadinessEvaluatedInterface;
}
