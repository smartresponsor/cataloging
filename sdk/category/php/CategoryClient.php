<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Sdk\Category;

final readonly class CategoryClient
{
    public function __construct(private string $baseUri)
    {
    }

    public function list(): array
    {
        $json = file_get_contents($this->baseUri.'/api/category/storefront');

        if (false === $json) {
            return [];
        }

        return json_decode($json, true) ?? [];
    }
}
