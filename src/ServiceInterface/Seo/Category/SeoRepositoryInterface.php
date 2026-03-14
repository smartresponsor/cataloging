<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\ServiceInterface\Seo\Category;

interface SeoRepositoryInterface
{
    public function save(array $input): void;

    public function find(string $categoryId, string $locale): ?array;
}
