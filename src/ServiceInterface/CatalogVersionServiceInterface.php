<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

/**
 * Defines the contract for version.
 */
interface CatalogVersionServiceInterface
{
    /**
     * Handles the id workflow.
     */
    public function id(): string;

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string;

    /**
     * Handles the number workflow.
     */
    public function number(): int;

    /**
     * Creates the d at result for the current workflow.
     */
    public function createdAt(): \DateTimeImmutable;
}
