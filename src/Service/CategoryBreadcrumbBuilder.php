<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\RepositoryInterface\Catalog\CatalogCategoryRepositoryInterface;
use App\Cataloging\ServiceInterface\CategoryBreadcrumbBuilderInterface;

/**
 * Provides the category breadcrumb builder application service.
 */
final readonly class CategoryBreadcrumbBuilder implements CategoryBreadcrumbBuilderInterface
{
    /**
     * Initializes the category breadcrumb builder service collaborators.
     */
    public function __construct(private CatalogCategoryRepositoryInterface $repo)
    {
    }

    /**
     * @return array{
     *     breadcrumb: list<array{id:string,name:string,slug:string}>,
     *     seo: array{fullSlug: string, title: string},
     * }
     */
    public function build(string $categoryId, string $locale): array
    {
        $chain = $this->normalizeChain($this->repo->breadcrumb($categoryId, $locale));
        $fullSlug = implode('/', array_map(static fn (array $node): string => $node['slug'], $chain));
        $title = implode(' / ', array_map(static fn (array $node): string => $node['name'], $chain));

        return ['breadcrumb' => $chain, 'seo' => ['fullSlug' => $fullSlug, 'title' => $title]];
    }

    /**
     * @return list<array{id:string,name:string,slug:string}>
     */
    private function normalizeChain(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }
        $chain = [];
        foreach ($value as $node) {
            if (!is_array($node)) {
                continue;
            }
            $id = $node['id'] ?? null;
            $name = $node['name'] ?? null;
            $slug = $node['slug'] ?? null;
            if (!is_scalar($id) || !is_scalar($name) || !is_scalar($slug)) {
                continue;
            }
            $chain[] = ['id' => (string) $id, 'name' => (string) $name, 'slug' => (string) $slug];
        }

        return $chain;
    }
}
