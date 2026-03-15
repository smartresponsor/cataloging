<?php

declare(strict_types=1);

namespace App\ServiceInterface;

interface ShopifyMapperInterface
{
    public function map(array $shopify): array;
}
