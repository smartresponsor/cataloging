<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>.
 */
declare(strict_types=1);

namespace App\ServiceInterface\Workflow\Category;

interface ProjectionRunnerInterface
{
    public function runOnce(): void;

    public function lag(): int;
}
