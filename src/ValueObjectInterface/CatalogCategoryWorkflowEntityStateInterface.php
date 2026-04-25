<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObjectInterface;

/**
 * Defines the contract for category workflow state value object.
 */
interface CatalogCategoryWorkflowEntityStateInterface
{
    /**
     * Returns the normalized workflow state string.
     */
    public function value(): string;
}
