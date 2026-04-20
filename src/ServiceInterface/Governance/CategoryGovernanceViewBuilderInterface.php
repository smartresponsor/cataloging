<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface\Governance;

use App\Cataloging\Projection\CategoryGovernanceView;

/**
 * Defines the contract for category governance view builder.
 */
interface CategoryGovernanceViewBuilderInterface
{
    /**
     * Builds the requested output for the current workflow.
     */
    public function build(string $categoryId): CategoryGovernanceView;
}
