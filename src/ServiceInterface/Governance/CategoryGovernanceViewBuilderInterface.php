<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface\Governance;

use App\Cataloging\Projection\CategoryGovernanceView;

interface CategoryGovernanceViewBuilderInterface
{
    public function build(string $categoryId): CategoryGovernanceView;
}
