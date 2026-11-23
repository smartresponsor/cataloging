<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface\Traceability;

use App\Cataloging\Projection\CategoryActorTraceabilityView;

interface CategoryActorTraceabilityViewBuilderInterface
{
    public function build(string $categoryId): CategoryActorTraceabilityView;
}
