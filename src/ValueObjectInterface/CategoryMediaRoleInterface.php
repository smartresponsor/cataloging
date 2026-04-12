<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObjectInterface;

/**
 * Defines the contract for category media role.
 */
interface CategoryMediaRoleInterface
{
    /**
     * Handles the value workflow.
     */
    public function value(): string;
}
