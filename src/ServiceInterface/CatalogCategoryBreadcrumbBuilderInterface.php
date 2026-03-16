<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

interface CatalogCategoryBreadcrumbBuilderInterface
{
    /** @return array{breadcrumb: array<int, array{id:string,name:string,slug:string}>, seo: array{fullSlug:string,title:string}} */
    public function build(string $categoryId, string $locale): array;
}
