<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\ControllerInterface\CategoryControllerInterface;
use App\RepositoryInterface\CategoryRepositoryInterface;
use App\ServiceInterface\CategoryBreadcrumbBuilderInterface;
use App\ServiceInterface\CategoryInterface as CatalogCategoryService;

final class CategoryController implements CategoryControllerInterface
{
    private CatalogCategoryService $service;
    private CategoryRepositoryInterface $repo;
    private CategoryBreadcrumbBuilderInterface $breadcrumb;

    public function __construct(CatalogCategoryService $service, CategoryRepositoryInterface $repo, CategoryBreadcrumbBuilderInterface $breadcrumb)
    {
        $this->service = $service;
        $this->repo = $repo;
        $this->breadcrumb = $breadcrumb;
    }

    /** @return list<array<string, mixed>> */
    public function tree(array $query, array $route): array
    {
        $taxonomy = $this->requiredString($route, 'taxonomy');
        $locale = $this->stringFromMap($query, 'locale', 'en');
        $depth = $this->intFromMap($query, 'depth', 2);
        $parentId = $this->nullableStringFromMap($query, 'parentId');
        $tree = $this->repo->tree($taxonomy, $parentId, $depth, $locale);

        return $this->listOfMaps($tree);
    }

    /** @return array{category: array<string, mixed>, breadcrumb: mixed, seo: mixed} */
    public function bySlug(array $query, array $route): array
    {
        $taxonomy = $this->requiredString($route, 'taxonomy');
        $slug = $this->requiredString($route, 'slug');
        $locale = $this->stringFromMap($query, 'locale', 'en');
        $cat = $this->repo->bySlug($taxonomy, $slug, $locale);
        $categoryId = $this->requiredString($cat, 'id');
        $crumb = $this->breadcrumb->build($categoryId, $locale);

        return ['category' => $cat, 'breadcrumb' => $crumb['breadcrumb'], 'seo' => $crumb['seo']];
    }

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $route
     * @param array<string, mixed> $auth
     *
     * @return array<string, mixed>
     */
    public function create(array $body, array $route, array $auth): array
    {
        $actorId = $this->requiredString($auth, 'actorId');
        $taxonomyId = $this->requiredString($body, 'taxonomyId');
        $parentId = $this->nullableStringFromMap($body, 'parentId');
        $name = $this->stringMap($body, 'name');
        $slug = $this->stringMap($body, 'slug');
        $meta = $this->metaMap($body, 'meta');

        return $this->service->create($actorId, $taxonomyId, $parentId, $name, $slug, $meta);
    }

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $route
     * @param array<string, mixed> $auth
     *
     * @return array<string, mixed>
     */
    public function move(array $body, array $route, array $auth): array
    {
        $actorId = $this->requiredString($auth, 'actorId');
        $categoryId = $this->requiredString($route, 'id');
        $parentId = $this->nullableStringFromMap($body, 'parentId');
        $order = $this->intFromMap($body, 'order', 0);

        return $this->service->move($actorId, $categoryId, $parentId, $order);
    }

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $route
     * @param array<string, mixed> $auth
     */
    public function attach(array $body, array $route, array $auth): void
    {
        $actorId = $this->requiredString($auth, 'actorId');
        $categoryId = $this->requiredString($route, 'id');
        $this->service->attach(
            $actorId,
            $categoryId,
            $this->requiredString($body, 'targetDomain'),
            $this->requiredString($body, 'targetClass'),
            $this->requiredString($body, 'targetId'),
        );
    }

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $route
     * @param array<string, mixed> $auth
     */
    public function detach(array $body, array $route, array $auth): void
    {
        $actorId = $this->requiredString($auth, 'actorId');
        $categoryId = $this->requiredString($route, 'id');
        $this->service->detach(
            $actorId,
            $categoryId,
            $this->requiredString($body, 'targetDomain'),
            $this->requiredString($body, 'targetClass'),
            $this->requiredString($body, 'targetId'),
        );
    }

    /** @param array<string, mixed> $map */
    private function requiredString(array $map, string $key): string
    {
        return $this->stringFromMap($map, $key);
    }

    /** @param array<string, mixed> $map */
    private function stringFromMap(array $map, string $key, string $default = ''): string
    {
        $value = $map[$key] ?? $default;

        return is_scalar($value) ? (string) $value : $default;
    }

    /** @param array<string, mixed> $map */
    private function nullableStringFromMap(array $map, string $key): ?string
    {
        $value = $map[$key] ?? null;
        if (null === $value || '' === $value) {
            return null;
        }

        return is_scalar($value) ? (string) $value : null;
    }

    /** @param array<string, mixed> $map */
    private function intFromMap(array $map, string $key, int $default = 0): int
    {
        $value = $map[$key] ?? $default;

        return is_numeric($value) ? (int) $value : $default;
    }

    /**
     * @param array<string, mixed> $map
     *
     * @return array<string, string>
     */
    private function stringMap(array $map, string $key): array
    {
        $value = $map[$key] ?? [];
        if (!is_array($value)) {
            return [];
        }

        $normalized = [];
        foreach ($value as $entryKey => $entryValue) {
            if (!is_string($entryKey) || !is_scalar($entryValue)) {
                continue;
            }
            $normalized[$entryKey] = (string) $entryValue;
        }

        return $normalized;
    }

    /**
     * @param array<string, mixed> $map
     *
     * @return array<string, array<string, bool|float|int|string|null>|bool|float|int|string|null>
     */
    private function metaMap(array $map, string $key): array
    {
        $value = $map[$key] ?? [];
        if (!is_array($value)) {
            return [];
        }

        $normalized = [];
        foreach ($value as $entryKey => $entryValue) {
            if (!is_string($entryKey)) {
                continue;
            }
            if (is_array($entryValue)) {
                $nested = [];
                foreach ($entryValue as $nestedKey => $nestedValue) {
                    if (!is_string($nestedKey)) {
                        continue;
                    }
                    if (is_bool($nestedValue) || is_float($nestedValue) || is_int($nestedValue) || is_string($nestedValue) || null === $nestedValue) {
                        $nested[$nestedKey] = $nestedValue;
                    }
                }
                $normalized[$entryKey] = $nested;
                continue;
            }
            if (is_bool($entryValue) || is_float($entryValue) || is_int($entryValue) || is_string($entryValue) || null === $entryValue) {
                $normalized[$entryKey] = $entryValue;
            }
        }

        return $normalized;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function listOfMaps(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $items = [];
        foreach ($value as $item) {
            if (is_array($item)) {
                $items[] = $item;
            }
        }

        return array_values($items);
    }
}
