<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the noindex policy application service.
 */
final class NoindexPolicy
{
    /**
     * Handles the should noindex workflow.
     */
    public function shouldNoindex(bool $virtual): bool
    {
        return $virtual;
    }
}
