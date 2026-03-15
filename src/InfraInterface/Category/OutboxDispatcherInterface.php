<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\InfraInterface\Category;

interface OutboxDispatcherInterface
{
    public function dispatch(array $event): void;
}
