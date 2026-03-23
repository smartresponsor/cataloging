<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Governance;

use App\Projection\CategoryGovernanceView;

interface CategoryGovernanceViewBuilderInterface
{
    public function build(string $categoryId): CategoryGovernanceView;
}
