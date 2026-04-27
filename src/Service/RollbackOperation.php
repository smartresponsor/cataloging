<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the rollback operation application service.
 */
final class RollbackOperation
{
    /**
     * Handles the rollback workflow.
     */
    public function rollback(CatalogVersionService $target): void
    {
        // Application layer should restore state from the target version snapshot.
    }
}
