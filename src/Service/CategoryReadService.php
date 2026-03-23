<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Entity\CategoryEntity;
use App\Repository\CatalogRepository;
use App\ServiceInterface\CategoryReadServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;

final class CategoryReadService implements CategoryReadServiceInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CatalogRepository $catalogRepository,
        private readonly CacheInterface $cache,
    ) {
    }

    public function childList(string $id): ?array
    {
        /** @var CategoryEntity|null $node */
        $node = $this->entityManager->getRepository(CategoryEntity::class)->find($id);
        if (null === $node) {
            return null;
        }

        $children = $this->cache->get('cat_child_'.$node->getId(), fn () => $this->catalogRepository->findChildrenLtree($node));

        return array_map(
            static fn (CategoryEntity $category): array => [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'slug' => $category->getSlug(),
                'path' => $category->getPath(),
                'depth' => $category->getDepth(),
            ],
            $children,
        );
    }

    public function ancestorList(string $id): ?array
    {
        /** @var CategoryEntity|null $node */
        $node = $this->entityManager->getRepository(CategoryEntity::class)->find($id);
        if (null === $node) {
            return null;
        }

        $ancestors = $this->cache->get('cat_anc_'.$node->getId(), fn () => $this->catalogRepository->findAncestorsLtree($node));

        return array_map(
            static fn (CategoryEntity $category): array => [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'slug' => $category->getSlug(),
                'path' => $category->getPath(),
                'depth' => $category->getDepth(),
            ],
            $ancestors,
        );
    }

    public function list(int $first, string $after): array
    {
        $qb = $this->entityManager->getRepository(CategoryEntity::class)
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
        $next = '';
        if (count($list) === $first) {
            $last = end($list);
            $next = is_array($last) && isset($last['path']) ? base64_encode((string) $last['path']) : '';
        }

        return ['item' => $list, 'after' => $next];
    }
}
