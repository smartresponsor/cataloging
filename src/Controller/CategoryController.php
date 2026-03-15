<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\ControllerInterface\CategoryControllerInterface;
use App\RepositoryInterface\CategoryRepositoryInterface;
use App\ServiceInterface\CatalogCategoryInterface as CategoryService;

final class CategoryController implements CategoryControllerInterface
{
    private CategoryService $service;
    private CategoryRepositoryInterface $repo;
    private CategoryBreadcrumbBuilderInterface $breadcrumb;

    public function __construct(CategoryService $service, CategoryRepositoryInterface $repo, CategoryBreadcrumbBuilderInterface $breadcrumb)
    {
        $this->service = $service;
        $this->repo = $repo;
        $this->breadcrumb = $breadcrumb;
    }

    public function tree(array $query, array $route): array
    {
        $taxonomy = (string) $route['taxonomy'];
        $locale = (string) ($query['locale'] ?? 'en');
        $depth = (int) ($query['depth'] ?? 2);
        $parentId = $query['parentId'] ?? null;

        return $this->repo->tree($taxonomy, $parentId, $depth, $locale);
    }

    public function bySlug(array $query, array $route): array
    {
        $taxonomy = (string) $route['taxonomy'];
        $slug = (string) $route['slug'];
        $locale = (string) ($query['locale'] ?? 'en');
        $cat = $this->repo->bySlug($taxonomy, $slug, $locale);
        $crumb = $this->breadcrumb->build($cat['id'], $locale);

        return ['category' => $cat, 'breadcrumb' => $crumb['breadcrumb'], 'seo' => $crumb['seo']];
    }

    public function create(array $body, array $route, array $auth): array
    {
        $actorId = (string) $auth['actorId'];
        $taxonomyId = (string) $body['taxonomyId'];
        $parentId = $body['parentId'] ?? null;
        $name = (array) ($body['name'] ?? []);
        $slug = (array) ($body['slug'] ?? []);
        $meta = (array) ($body['meta'] ?? []);

        return $this->service->create($actorId, $taxonomyId, $parentId, $name, $slug, $meta);
    }

    public function move(array $body, array $route, array $auth): array
    {
        $actorId = (string) $auth['actorId'];
        $categoryId = (string) $route['id'];
        $parentId = $body['parentId'] ?? null;
        $order = (int) ($body['order'] ?? 0);

        return $this->service->move($actorId, $categoryId, $parentId, $order);
    }

    public function attach(array $body, array $route, array $auth): void
    {
        $actorId = (string) $auth['actorId'];
        $categoryId = (string) $route['id'];
        $this->service->attach($actorId, $categoryId, (string) $body['targetDomain'], (string) $body['targetClass'], (string) $body['targetId']);
    }

    public function detach(array $body, array $route, array $auth): void
    {
        $actorId = (string) $auth['actorId'];
        $categoryId = (string) $route['id'];
        $this->service->detach($actorId, $categoryId, (string) $body['targetDomain'], (string) $body['targetClass'], (string) $body['targetId']);
    }
}
