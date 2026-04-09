<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\EntityInterface;

/**
 * Defines the contract for category redirect.
 */
interface CategoryRedirectInterface
{
    /**
     * Handles the id workflow.
     */
    public function id(): string;

    /**
     * Handles the frm workflow.
     */
    public function frm(): string;

    /**
     * Handles the to workflow.
     */
    public function to(): string;

    /**
     * Creates the d at result for the current workflow.
     */
    public function createdAt(): \DateTimeImmutable;
}
