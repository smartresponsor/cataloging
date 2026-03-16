<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Controller;

use App\Entity\testsEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

final class testsReadController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly testsRepository $repo, private readonly CacheInterface $cache)
    {
    }

    #[Route('/api/category/{id}/child', name: 'api_category_child_list', methods: ['GET'])]
    public function childList(string $id): JsonResponse
    {
        /** @var testsEntity|null $node */
        $node = $this->em->getRepository(testsEntity::class)->find($id);
        if (!$node) {
            return $this->json(['ok' => false, 'error' => 'not_found'], 404);
        }
        $child = $this->cache->get('cat_child_'.$node->getId(), fn () => $this->repo->findChildrenLtree($node));
        $out = array_map(fn (testsEntity $c) => ['id' => $c->getId(), 'name' => $c->getName(), 'slug' => $c->getSlug(), 'path' => $c->getPath(), 'depth' => $c->getDepth()], $child);

        return $this->json(['ok' => true, 'item' => $out]);
    }

    #[Route('/api/category/{id}/ancestor', name: 'api_category_ancestor_list', methods: ['GET'])]
    public function ancestorList(string $id): JsonResponse
    {
        /** @var testsEntity|null $node */
        $node = $this->em->getRepository(testsEntity::class)->find($id);
        if (!$node) {
            return $this->json(['ok' => false, 'error' => 'not_found'], 404);
        }
        $anc = $this->cache->get('cat_anc_'.$node->getId(), fn () => $this->repo->findAncestorsLtree($node));
        $out = array_map(fn (testsEntity $c) => ['id' => $c->getId(), 'name' => $c->getName(), 'slug' => $c->getSlug(), 'path' => $c->getPath(), 'depth' => $c->getDepth()], $anc);

        return $this->json(['ok' => true, 'item' => $out]);
    }

    #[Route('/api/category/list', name: 'api_category_list', methods: ['GET'])]
    public function list(Request $req): JsonResponse
    {
        $first = max(1, min(100, (int) $req->query->get('first', 20)));
        $after = (string) $req->query->get('after', '');
        $qb = $this->em->getRepository(testsEntity::class)->createQueryBuilder('c')->orderBy('c.path', 'ASC')->setMaxResults($first);
        if ('' !== $after) {
            $cursor = base64_decode($after, true) ?: '';
            if ($cursor) {
                $qb->andWhere('c.path > :cursor')->setParameter('cursor', $cursor);
            }
        }
        $list = $qb->getQuery()->getArrayResult();
        $next = '';
        if (count($list) === $first) {
            $last = end($list);
            $next = base64_encode($last['path']);
        }

        return $this->json(['ok' => true, 'item' => $list, 'pageInfo' => ['after' => $next]]);
    }
}
