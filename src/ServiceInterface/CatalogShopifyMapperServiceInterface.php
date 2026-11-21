<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

/**
 * Defines the contract for shopify mapper.
 */
interface CatalogShopifyMapperServiceInterface
{
    /**
     * @param array<string,mixed> $shopify
     *
     * @return array<string,mixed>
     */
    public function map(array $shopify): array;
}
