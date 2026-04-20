<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Observability;

/**
 * Provides the catalog projection metrics implementation.
 */
final class CatalogProjectionMetrics
{
    private int $lag = 0;

    /**
     * Updates the lag value.
     */
    public function setLag(int $seconds): void
    {
        $this->lag = $seconds;
    }

    /**
     * Returns the lag value.
     */
    public function getLag(): int
    {
        return $this->lag;
    }
}
