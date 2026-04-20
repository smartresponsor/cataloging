<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface\Ops;

use App\Cataloging\Projection\CategoryRuntimeStatusView;

/**
 * Defines the contract for category runtime status view builder.
 */
interface CategoryRuntimeStatusViewBuilderInterface
{
    /**
     * Builds the requested output for the current workflow.
     */
    public function build(string $categoryId): CategoryRuntimeStatusView;
}
