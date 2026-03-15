<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\RepositoryInterface\CategoryRepositoryInterface;

/** Builds breadcrumb chain and SEO meta from repository. */
final class CatalogCategoryBreadcrumbBuilder implements CategoryBreadcrumbBuilderInterface
{
    private CategoryRepositoryInterface $repo;

    public function __construct(CategoryRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function build(string $categoryId, string $locale): array
    {
        $chain = $this->repo->breadcrumb($categoryId, $locale);
        $fullSlug = implode('/', array_map(fn ($n) => $n['slug'], $chain));
        $title = implode(' / ', array_map(fn ($n) => $n['name'], $chain));

        return ['breadcrumb' => $chain, 'seo' => ['fullSlug' => $fullSlug, 'title' => $title]];
    }
}
