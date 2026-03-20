<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */
declare(strict_types=1);

namespace App\ServiceInterface;

interface ProjectionRunnerInterface
{
    public function runOnce(): void;

    public function lag(): int;
}
