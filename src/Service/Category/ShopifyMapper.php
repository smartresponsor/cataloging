<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Layer\Category;

final class ShopifyMapper
{
    public function map(array $shopify): array
    {
        $seoTitle = $shopify['metafield']['seo_title'] ?? null;
        $redirect = $shopify['redirect'] ?? null;

        return [
            'id' => (string) ($shopify['id'] ?? ''),
            'parentId' => $shopify['parent_id'] ?? null,
            'slug' => $shopify['handle'] ?? '',
            'name' => $shopify['title'] ?? '',
            'locale' => 'en',
            'seoTitle' => $seoTitle,
            'redirect' => $redirect,
        ];
    }
}
