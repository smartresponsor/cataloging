<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface\Ops;

use App\Cataloging\Projection\CategoryRuntimeStatusView;

interface CategoryRuntimeStatusViewBuilderInterface
{
    public function build(string $categoryId): CategoryRuntimeStatusView;
}
