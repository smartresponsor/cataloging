<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Category;

interface CatalogCollectionServiceInterface
{
    /**
     * @param list<array<string,mixed>> $products
     *
     * @return list<array<string,mixed>>
     */
    public function filter(array $products, string $rule): array;
}
