<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Traceability;

use App\Projection\CategoryActorTraceabilityView;

interface CategoryActorTraceabilityViewBuilderInterface
{
    public function build(string $categoryId): CategoryActorTraceabilityView;
}
