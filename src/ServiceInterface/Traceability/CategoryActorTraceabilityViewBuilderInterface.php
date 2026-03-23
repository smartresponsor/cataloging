<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ServiceInterface\Traceability;

use App\Projection\CategoryActorTraceabilityView;

interface CategoryActorTraceabilityViewBuilderInterface
{
    public function build(string $categoryId): CategoryActorTraceabilityView;
}
