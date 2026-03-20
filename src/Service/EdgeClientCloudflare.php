<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\EdgeClientInterface;

final class EdgeClientCloudflare implements EdgeClientInterface
{
    public function purge(string $url): bool
    {
        // Call Cloudflare API. Return true on success.
        return true;
    }
}
