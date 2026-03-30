<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Entity\CategoryEntity;
use App\Repository\CatalogRepository;
use App\ServiceInterface\CatalogReadServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;

final class CatalogReadService implements CatalogReadServiceInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CacheInterface $cache,
        private readonly CatalogRepository $catalogRepository,
    ) {
    }

    /** @return array{id:string,name:string,slug:string,path:string,depth:int}|null */
    public function byId(string $id): ?array
    {
        $category = $this->findCategory($id);
        if (null === $category) {
            return null;
        }

        return $this->normalizeCategory($category);
    }

    /** @return array{id:string,name:string,slug:string,path:string,depth:int,children:list<array{id:string,name:string,slug:string,path:string,depth:int}>}|null */
    public function descendantsTree(string $id): ?array
    {
        $node = $this->findCategory($id);
        if (null === $node) {
            return null;
        }

        $descendants = $this->cache->get(
            'cat_tree_'.$node->getId(),
            fn (): array => $this->catalogRepository->findDescendantsLtree($node),
        );

        return [
            ...$this->normalizeCategory($node),
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
            'cat_children_'.$node->getId(),
            fn (): array => $this->catalogRepository->findChildrenLtree($node),
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
            'cat_anc_'.$node->getId(),
            fn (): array => $this->catalogRepository->findAncestorsLtree($node),
        );

        return $this->normalizeCategories($ancestors);
    }

    /** @return array{item:list<array{id:string,name:string,slug:string,path:string,depth:int}>,after:string} */
    public function list(int $first, string $after): array
    {
        $qb = $this->entityManager
            ->getRepository(CategoryEntity::class)
            ->createQueryBuilder('c')
            ->orderBy('c.path', 'ASC')
            ->setMaxResults($first);

        if ('' !== $after) {
            $cursor = base64_decode($after, true) ?: '';
            if ('' !== $cursor) {
                $qb->andWhere('c.path > :cursor')->setParameter('cursor', $cursor);
            }
        }

        $list = $qb->getQuery()->getArrayResult();
        $normalized = [];
        foreach ($list as $row) {
            if (!is_array($row)) {
                continue;
            }

            $normalized[] = [
                'id' => is_scalar($row['id'] ?? null) ? (string) $row['id'] : '',
                'name' => is_scalar($row['name'] ?? null) ? (string) $row['name'] : '',
                'slug' => is_scalar($row['slug'] ?? null) ? (string) $row['slug'] : '',
                'path' => is_scalar($row['path'] ?? null) ? (string) $row['path'] : '',
                'depth' => is_numeric($row['depth'] ?? null) ? (int) $row['depth'] : 0,
            ];
        }

        $next = '';
        if (count($normalized) === $first) {
            $last = end($normalized);
            $path = is_array($last) ? $last['path'] : '';
            $next = is_string($path) && '' !== $path ? base64_encode($path) : '';
        }

        return ['item' => $normalized, 'after' => $next];
    }

    private function findCategory(string $id): ?CategoryEntity
    {
        $category = $this->entityManager->getRepository(CategoryEntity::class)->find($id);

        return $category instanceof CategoryEntity ? $category : null;
    }

    /** @return array{id:string,name:string,slug:string,path:string,depth:int} */
    private function normalizeCategory(CategoryEntity $category): array
    {
        return [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'slug' => $category->getSlug(),
            'path' => $category->getPath(),
            'depth' => $category->getDepth(),
        ];
    }

    /** @param iterable<CategoryEntity> $categories
     * @return list<array{id:string,name:string,slug:string,path:string,depth:int}>
     */
    private function normalizeCategories(iterable $categories): array
    {
        $result = [];
        foreach ($categories as $category) {
            if (!$category instanceof CategoryEntity) {
                continue;
            }
            $result[] = $this->normalizeCategory($category);
        }

        return $result;
    }
}
