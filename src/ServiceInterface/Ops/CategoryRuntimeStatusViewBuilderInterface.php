<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Ops;

use App\Projection\CategoryRuntimeStatusView;

interface CategoryRuntimeStatusViewBuilderInterface
{
    public function build(string $categoryId): CategoryRuntimeStatusView;
}
