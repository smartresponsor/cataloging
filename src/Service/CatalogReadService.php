<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Repository\Catalog\CatalogRepository;
use App\Cataloging\ServiceInterface\CatalogReadServiceInterface;
use App\Cataloging\ValueObject\CategoryCatalogReadNodeRequest;
use App\Cataloging\ValueObject\CategoryCatalogReadPageRequest;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Provides the catalog read service application service.
 */
final readonly class CatalogReadService implements CatalogReadServiceInterface
{
    /**
     * Initializes the catalog read service service collaborators.
     */
    public function __construct(
        private CacheInterface $cache,
        private CatalogRepository $catalogRepository,
        private CatalogCategoryRowNormalizer $rowNormalizer,
    ) {
    }

    /**
     * @return array{id:string,name:string,slug:string,path:string,depth:int}|null
     */
    public function byId(CategoryCatalogReadNodeRequest $request): ?array
    {
        return $this->findCategory($request);
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
     *
     * @throws InvalidArgumentException
     */
    public function descendantsTree(CategoryCatalogReadNodeRequest $request): ?array
    {
        $node = $this->findCategory($request);
        if (null === $node) {
            return null;
        }

        $descendants = $this->cache->get(
            'cat_tree_'.$node['id'],
            fn (): array => $this->catalogRepository->findDescendantRowsByPath($node['path']),
        );

        return [
            ...$node,
            'children' => $this->rowNormalizer->normalize($descendants),
        ];
    }

    /**
     * @return list<array{id:string,name:string,slug:string,path:string,depth:int}>|null
     *
     * @throws InvalidArgumentException
     */
    public function childList(CategoryCatalogReadNodeRequest $request): ?array
    {
        $node = $this->findCategory($request);
        if (null === $node) {
            return null;
        }

        $children = $this->cache->get(
            'cat_children_'.$node['id'],
            fn (): array => $this->catalogRepository->findChildrenRowsByPath($node['path']),
        );

        return $this->rowNormalizer->normalize($children);
    }

    /**
     * @return list<array{id:string,name:string,slug:string,path:string,depth:int}>|null
     *
     * @throws InvalidArgumentException
     */
    public function childrenList(CategoryCatalogReadNodeRequest $request): ?array
    {
        return $this->childList($request);
    }

    /**
     * @return list<array{id:string,name:string,slug:string,path:string,depth:int}>|null
     *
     * @throws InvalidArgumentException
     */
    public function ancestorList(CategoryCatalogReadNodeRequest $request): ?array
    {
        $node = $this->findCategory($request);
        if (null === $node) {
            return null;
        }

        $ancestors = $this->cache->get(
            'cat_anc_'.$node['id'],
            fn (): array => $this->catalogRepository->findAncestorRowsByPath($node['path']),
        );

        return $this->rowNormalizer->normalize($ancestors);
    }

    /**
     * @return array{item:list<array{id:string,name:string,slug:string,path:string,depth:int}>,after:string}
     */
    public function list(CategoryCatalogReadPageRequest $request): array
    {
        $rows = $this->catalogRepository->findPageRows($request->first(), $request->after());
        $normalized = $this->rowNormalizer->normalize($rows);

        $next = '';
        if (count($normalized) === $request->first()) {
            $last = end($normalized);
            if (is_array($last)) {
                $path = $last['path'];
                $next = '' !== $path ? base64_encode($path) : '';
            }
        }

        return ['item' => $normalized, 'after' => $next];
    }

    /**
     * @return array{id:string,name:string,slug:string,path:string,depth:int}|null
     */
    private function findCategory(CategoryCatalogReadNodeRequest $request): ?array
    {
        $normalizedId = $request->id();
        if ('' === $normalizedId) {
            return null;
        }

        return $this->catalogRepository->findNodeRowById($normalizedId);
    }
}
