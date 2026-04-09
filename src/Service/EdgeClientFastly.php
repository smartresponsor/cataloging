<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\EdgeClientInterface;
/**
 * Provides the edge client fastly application service.
 */
final class EdgeClientFastly implements EdgeClientInterface
{
    /**
     * Handles the purge workflow.
     */
    public function purge(string $url): bool
    {
        // Call Fastly API. Return true on success.
        return true;
    }
}
