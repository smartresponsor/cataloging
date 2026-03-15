<?php

declare(strict_types=1);

/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 */

namespace App\ServiceInterface\Category\Domain;

interface CollectionServiceInterface
{
    /** @return array<int,array<string,mixed>> */
    public function filter(array $products, string $rule): array;
}
