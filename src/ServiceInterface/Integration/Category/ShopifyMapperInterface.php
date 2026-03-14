<?php

declare(strict_types=1);

namespace App\ServiceInterface\Integration\Category;

interface ShopifyMapperInterface
{
    public function map(array $shopify): array;
}
