<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Category;
/**
 * Defines the contract for category quota service.
 */
interface CategoryQuotaServiceInterface
{
    /**
     * Handles the allow workflow.
     */
    public function allow(string $scope, string $id, string $op, int $capacity, float $ratePerSec): bool;
}
