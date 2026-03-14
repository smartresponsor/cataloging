<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Controller;

use App\ControllerInterface\CategoryControllerInterface;
use App\RepositoryInterface\CategoryRepositoryInterface;
use App\ServiceInterface\Command\Category\CategoryCommandServiceInterface as CategoryServiceInterface;
use App\ServiceInterface\Query\Category\CategoryBreadcrumbBuilderInterface;

final class CategoryController implements CategoryControllerInterface
{
    public function __construct(
        private readonly CategoryServiceInterface $service,
        private readonly CategoryRepositoryInterface $repository,
        private readonly CategoryBreadcrumbBuilderInterface $breadcrumbBuilder,
    ) {
    }

    public function tree(array $query, array $route): array
    {
        $taxonomy = (string) $route['taxonomy'];
        $locale = (string) ($query['locale'] ?? 'en');
        $depth = (int) ($query['depth'] ?? 2);
        $parentId = $query['parentId'] ?? null;

        return $this->repository->tree($taxonomy, $parentId, $depth, $locale);
    }

    public function bySlug(array $query, array $route): array
    {
        $taxonomy = (string) $route['taxonomy'];
        $slug = (string) $route['slug'];
        $locale = (string) ($query['locale'] ?? 'en');
        $category = $this->repository->bySlug($taxonomy, $slug, $locale);
        $breadcrumb = $this->breadcrumbBuilder->build($category['id'], $locale);

        return ['category' => $category, 'breadcrumb' => $breadcrumb['breadcrumb'], 'seo' => $breadcrumb['seo']];
    }

    public function create(array $body, array $route, array $auth): array
    {
        return $this->service->create(
            (string) $auth['actorId'],
            (string) $body['taxonomyId'],
            $body['parentId'] ?? null,
            (array) ($body['name'] ?? []),
            (array) ($body['slug'] ?? []),
            (array) ($body['meta'] ?? []),
        );
    }

    public function move(array $body, array $route, array $auth): array
    {
        return $this->service->move(
            (string) $auth['actorId'],
            (string) $route['id'],
            $body['parentId'] ?? null,
            (int) ($body['order'] ?? 0),
        );
    }

    public function attach(array $body, array $route, array $auth): void
    {
        $this->service->attach(
            (string) $auth['actorId'],
            (string) $route['id'],
            (string) $body['targetDomain'],
            (string) $body['targetClass'],
            (string) $body['targetId'],
        );
    }

    public function detach(array $body, array $route, array $auth): void
    {
        $this->service->detach(
            (string) $auth['actorId'],
            (string) $route['id'],
            (string) $body['targetDomain'],
            (string) $body['targetClass'],
            (string) $body['targetId'],
        );
    }
}
