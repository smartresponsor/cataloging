<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Category;

interface CatalogCollectionServiceInterface
{
    /** @return array<int,array<string,mixed>> */
    public function filter(array $products, string $rule): array;
}
