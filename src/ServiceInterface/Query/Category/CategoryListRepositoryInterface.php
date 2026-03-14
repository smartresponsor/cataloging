<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Query\Category;

interface CategoryListRepositoryInterface
{
    public function list(array $opt, bool $withTotal, bool $approxTotal): array;

    public function breadcrumb(string $id): array;
}
