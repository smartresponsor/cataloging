<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Repository\CatalogRepository;
use App\ServiceInterface\CatalogReadServiceInterface;
use Symfony\Contracts\Cache\CacheInterface;
/**
 * Provides the catalog read service application service.
 */
final class CatalogReadService implements CatalogReadServiceInterface
{
    /**
     * Initializes the catalog read service service collaborators.
     */
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly CatalogRepository $catalogRepository,
    ) {
    }

    /** @return array{id:string,name:string,slug:string,path:string,depth:int}|null */
    public function byId(string $id): ?array
    {
        return $this->findCategory($id);
    }

    /**
     * @return array{
     *     id:string,
     *     name:string,
     *     slug:string,
     *     path:string,
     *     depth:int,
     *     children:list<array{
     *         id:string,
     *         name:string,
     *         slug:string,
     *         path:string,
     *         depth:int,
     *     }>,
     * }|null
     */
    public function descendantsTree(string $id): ?array
    {
        $node = $this->findCategory($id);
        if (null === $node) {
            return null;
        }

        $descendants = $this->cache->get(
            'cat_tree_'.$node['id'],
            fn (): array => $this->catalogRepository->findDescendantRowsByPath($node['path']),
        );

        return [
            ...$node,
            'children' => $this->normalizeCategories($descendants),
        ];
    }

    /** @return list<array{id:string,name:string,slug:string,path:string,depth:int}>|null */
    public function childList(string $id): ?array
    {
        $node = $this->findCategory($id);
        if (null === $node) {
            return null;
        }

        $children = $this->cache->get(
            'cat_children_'.$node['id'],
            fn (): array => $this->catalogRepository->findChildrenRowsByPath($node['path']),
        );

        return $this->normalizeCategories($children);
    }

    /** @return list<array{id:string,name:string,slug:string,path:string,depth:int}>|null */
    public function childrenList(string $id): ?array
    {
        return $this->childList($id);
    }

    /** @return list<array{id:string,name:string,slug:string,path:string,depth:int}>|null */
    public function ancestorList(string $id): ?array
    {
        $node = $this->findCategory($id);
        if (null === $node) {
            return null;
        }

        $ancestors = $this->cache->get(
            'cat_anc_'.$node['id'],
            fn (): array => $this->catalogRepository->findAncestorRowsByPath($node['path']),
        );

        return $this->normalizeCategories($ancestors);
    }

    /** @return array{item:list<array{id:string,name:string,slug:string,path:string,depth:int}>,after:string} */
    public function list(int $first, string $after): array
    {
        $rows = $this->catalogRepository->findPageRows($first, $after);
        $normalized = $this->normalizeCategories($rows);

        $next = '';
        if (count($normalized) === $first) {
            $last = end($normalized);
            if (is_array($last)) {
                $path = $last['path'];
                $next = '' !== $path ? base64_encode($path) : '';
            }
        }

        return ['item' => $normalized, 'after' => $next];
    }

    /** @return array{id:string,name:string,slug:string,path:string,depth:int}|null */
    private function findCategory(string $id): ?array
    {
        $normalizedId = trim($id);
        if ('' === $normalizedId) {
            return null;
        }

        return $this->catalogRepository->findNodeRowById($normalizedId);
    }

    /** @param iterable<array<string,mixed>> $categories
     * @return list<array{id:string,name:string,slug:string,path:string,depth:int}>
     */
    private function normalizeCategories(iterable $categories): array
    {
        $result = [];
        foreach ($categories as $category) {
            if (!is_array($category)) {
                continue;
            }

            $result[] = [
                'id' => is_scalar($category['id'] ?? null) ? (string) $category['id'] : '',
                'name' => is_scalar($category['name'] ?? null) ? (string) $category['name'] : '',
                'slug' => is_scalar($category['slug'] ?? null) ? (string) $category['slug'] : '',
                'path' => is_scalar($category['path'] ?? null) ? (string) $category['path'] : '',
                'depth' => is_numeric($category['depth'] ?? null) ? (int) $category['depth'] : 0,
            ];
        }

        return $result;
    }
}
