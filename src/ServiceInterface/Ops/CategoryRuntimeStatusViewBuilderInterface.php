<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ServiceInterface\Ops;

use App\Projection\CategoryRuntimeStatusView;

interface CategoryRuntimeStatusViewBuilderInterface
{
    public function build(string $categoryId): CategoryRuntimeStatusView;
}
