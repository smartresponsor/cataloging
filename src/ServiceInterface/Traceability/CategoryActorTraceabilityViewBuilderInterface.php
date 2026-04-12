<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Traceability;

use App\Projection\CategoryActorTraceabilityView;

/**
 * Defines the contract for category actor traceability view builder.
 */
interface CategoryActorTraceabilityViewBuilderInterface
{
    /**
     * Builds the requested output for the current workflow.
     */
    public function build(string $categoryId): CategoryActorTraceabilityView;
}
