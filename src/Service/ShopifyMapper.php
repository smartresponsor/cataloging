<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the shopify mapper application service.
 */
final class ShopifyMapper
{
    /**
     * @param array<string,mixed> $shopify
     *
     * @return array{id:string,parentId:?string,slug:string,name:string,locale:string,seoTitle:?string,redirect:mixed}
     */
    public function map(array $shopify): array
    {
        $metafield = is_array($shopify['metafield'] ?? null) ? $shopify['metafield'] : [];
        $seoTitle = is_scalar($metafield['seo_title'] ?? null) ? (string) $metafield['seo_title'] : null;
        $redirect = $shopify['redirect'] ?? null;

        return [
            'id' => $this->stringValue($shopify, 'id'),
            'parentId' => $this->nullableStringValue($shopify, 'parent_id'),
            'slug' => $this->stringValue($shopify, 'handle'),
            'name' => $this->stringValue($shopify, 'title'),
            'locale' => 'en',
            'seoTitle' => $seoTitle,
            'redirect' => $redirect,
        ];
    }

    /** @param array<string,mixed> $input */
    private function stringValue(array $input, string $key): string
    {
        $value = $input[$key] ?? '';

        return is_scalar($value) ? (string) $value : '';
    }

    /** @param array<string,mixed> $input */
    private function nullableStringValue(array $input, string $key): ?string
    {
        $value = $input[$key] ?? null;

        return is_scalar($value) ? (string) $value : null;
    }
}
