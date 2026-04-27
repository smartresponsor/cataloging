<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the progress tracker application service.
 */
final class CatalogCatalogProgressTrackerServiceService
{
    private int $ok = 0;
    private int $fail = 0;

    /**
     * Handles the report workflow.
     */
    public function report(int $ok, int $fail): void
    {
        $this->ok += $ok;
        $this->fail += $fail;
    }

    /**
     * Handles the total ok workflow.
     */
    public function totalOk(): int
    {
        return $this->ok;
    }

    /**
     * Handles the total fail workflow.
     */
    public function totalFail(): int
    {
        return $this->fail;
    }
}
