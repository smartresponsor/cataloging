<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\ServiceInterface\Api\Category;

interface GraphqlResolverInterface
{
    public function category(array $args): ?array;

    public function categoryPath(array $args): array;

    public function publishCategory(array $args): ?array;

    public function moveCategory(array $args): bool;
}
