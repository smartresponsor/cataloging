<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CategoryEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

final class CategoryReadController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CategoryRepository $repository,
        private readonly CacheInterface $cache,
    ) {
    }

    #[Route('/api/catalog/{id}/child', name: 'api_category_child_list', methods: ['GET'])]
    public function childList(string $id): JsonResponse
    {
        /** @var CategoryEntity|null $node */
        $node = $this->entityManager->getRepository(CategoryEntity::class)->find($id);
        if (null === $node) {
            return $this->json(['ok' => false, 'error' => 'not_found'], 404);
        }

        $childList = $this->cache->get('cat_child_'.$node->getId(), fn () => $this->repository->findChildrenLtree($node));
        $itemList = array_map(
            static fn (CategoryEntity $child): array => [
                'id' => $child->getId(),
                'name' => $child->getName(),
                'slug' => $child->getSlug(),
                'path' => $child->getPath(),
                'depth' => $child->getDepth(),
            ],
            $childList,
        );

        return $this->json(['ok' => true, 'item' => $itemList]);
    }

    #[Route('/api/catalog/{id}/ancestor', name: 'api_category_ancestor_list', methods: ['GET'])]
    public function ancestorList(string $id): JsonResponse
    {
        /** @var CategoryEntity|null $node */
        $node = $this->entityManager->getRepository(CategoryEntity::class)->find($id);
        if (null === $node) {
            return $this->json(['ok' => false, 'error' => 'not_found'], 404);
        }

        $ancestorList = $this->cache->get('cat_anc_'.$node->getId(), fn () => $this->repository->findAncestorsLtree($node));
        $itemList = array_map(
            static fn (CategoryEntity $ancestor): array => [
                'id' => $ancestor->getId(),
                'name' => $ancestor->getName(),
                'slug' => $ancestor->getSlug(),
                'path' => $ancestor->getPath(),
                'depth' => $ancestor->getDepth(),
            ],
            $ancestorList,
        );

        return $this->json(['ok' => true, 'item' => $itemList]);
    }

    #[Route('/api/catalog/list', name: 'api_category_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $first = max(1, min(100, (int) $request->query->get('first', 20)));
        $after = (string) $request->query->get('after', '');
        $queryBuilder = $this->entityManager->getRepository(CategoryEntity::class)
            ->createQueryBuilder('c')
            ->orderBy('c.path', 'ASC')
            ->setMaxResults($first);

        if ('' !== $after) {
            $cursor = base64_decode($after, true) ?: '';
            if ('' !== $cursor) {
                $queryBuilder->andWhere('c.path > :cursor')->setParameter('cursor', $cursor);
            }
        }

        $list = $queryBuilder->getQuery()->getArrayResult();
        $next = '';
        if (count($list) === $first) {
            $last = end($list);
            if (is_array($last) && isset($last['path'])) {
                $next = base64_encode((string) $last['path']);
            }
        }

        return $this->json(['ok' => true, 'item' => $list, 'pageInfo' => ['after' => $next]]);
    }
}
