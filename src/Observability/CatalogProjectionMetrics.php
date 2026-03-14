<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Observability;

final class CatalogProjectionMetrics
{
    private int $lag = 0;

    public function setLag(int $seconds): void
    {
        $this->lag = $seconds;
    }

    public function getLag(): int
    {
        return $this->lag;
    }
}
