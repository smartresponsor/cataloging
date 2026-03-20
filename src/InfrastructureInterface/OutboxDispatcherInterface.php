<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\InfrastructureInterface;

interface OutboxDispatcherInterface
{
    public function dispatch(array $event): void;
}
