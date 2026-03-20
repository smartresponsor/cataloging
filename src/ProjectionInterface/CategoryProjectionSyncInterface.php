<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ProjectionInterface;

interface CategoryProjectionSyncInterface
{
    /** Apply domain event payloads to MySQL read models. */
    public function apply(array $event): void;
}
