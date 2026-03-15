<?php

declare(strict_types=1);

namespace App\Layer\Category;

interface ShopifyMapperInterface
{
    public function map(array $shopify): array;
}
