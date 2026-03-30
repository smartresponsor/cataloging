<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

interface ShopifyMapperInterface
{
    /**
     * @param array<string,mixed> $shopify
     *
     * @return array<string,mixed>
     */
    public function map(array $shopify): array;
}
