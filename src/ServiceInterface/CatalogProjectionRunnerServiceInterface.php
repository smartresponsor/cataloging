<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

/**
 * Defines the contract for projection runner.
 */
interface CatalogProjectionRunnerServiceInterface
{
    /**
     * Handles the run once workflow.
     */
    public function runOnce(): void;

    /**
     * Handles the lag workflow.
     */
    public function lag(): int;
}
