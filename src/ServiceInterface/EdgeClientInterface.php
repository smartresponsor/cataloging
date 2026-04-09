<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;
/**
 * Defines the contract for edge client.
 */
interface EdgeClientInterface
{
    /**
     * Handles the purge workflow.
     */
    public function purge(string $url): bool;
}
