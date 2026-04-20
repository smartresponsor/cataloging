<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries audit context for catalog workflows.
 */
final readonly class CatalogAuditContext
{
    /**
     * Initializes the catalog audit context value object.
     */
    public function __construct(
        private string $actorId,
        private string $reason,
    ) {
    }

    public function actorId(): string
    {
        return $this->actorId;
    }

    public function reason(): string
    {
        return $this->reason;
    }
}
