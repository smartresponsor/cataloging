<?php
declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */
namespace SmartResponsor\Sdk\Category;

final class CategoryClient
{
    public function __construct(private readonly string $baseUri)
    {
    }
    public function list(): array
    {
        $json = @file_get_contents($this->baseUri.'/api/catalog/storefront') ?: '[]';
        return json_decode($json, true) ?? [];
    }
}
