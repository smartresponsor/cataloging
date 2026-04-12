<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

/**
 * Provides the rollback operation application service.
 */
final class RollbackOperation
{
    /**
     * Handles the rollback workflow.
     */
    public function rollback(Version $target): void
    {
        // Application layer should restore state from the target version snapshot.
    }
}
