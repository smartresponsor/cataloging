<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>.
 */
declare(strict_types=1);

namespace App\ServiceInterface\Api\Category;

interface EdgeClientInterface
{
    public function purge(string $url): bool;
}
