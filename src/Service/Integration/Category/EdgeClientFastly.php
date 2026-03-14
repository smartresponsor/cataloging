<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>.
 */
declare(strict_types=1);

namespace App\Service\Integration\Category;

use App\ServiceInterface\Api\Category\EdgeClientInterface;

final class EdgeClientFastly implements EdgeClientInterface
{
    public function purge(string $url): bool
    {
        // Call Fastly API. Return true on success.
        return true;
    }
}
