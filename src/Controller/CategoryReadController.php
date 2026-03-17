<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

final class CategoryReadController
{
    public function __construct(private readonly CategoryRepository $repo, private readonly CacheInterface $cache)
    {
    }

    #[Route('/api/category/{id}/child', name: 'api_category_child_list', methods: ['GET'])]
    public function childList(string $id, ?Request $request = null): JsonResponse
    {
        $locale = (string) ($request?->query->get('locale', 'en') ?? 'en');
        $cacheKey = sprintf('cat_child_%s_%s', $id, $locale);
        $node = $this->repo->findById($id, $locale);
        if ([] === $node) {
            return new JsonResponse(['ok' => false, 'error' => 'not_found', 'id' => $id], 404);
        }

        $children = $this->cache->get($cacheKey, fn (): array => $this->repo->childrenOf($id, $locale, true));

        return new JsonResponse([
            'ok' => true,
            'data' => $children,
            'count' => count($children),
            'node' => $this->summarizeNode($node),
            'locale' => $locale,
        ]);
    }

    #[Route('/api/category/{id}/ancestor', name: 'api_category_ancestor_list', methods: ['GET'])]
    public function ancestorList(string $id, ?Request $request = null): JsonResponse
    {
        $locale = (string) ($request?->query->get('locale', 'en') ?? 'en');
        $cacheKey = sprintf('cat_anc_%s_%s', $id, $locale);
        $node = $this->repo->findById($id, $locale);
        if ([] === $node) {
            return new JsonResponse(['ok' => false, 'error' => 'not_found', 'id' => $id], 404);
        }

        $ancestors = $this->cache->get($cacheKey, fn (): array => $this->repo->ancestorsOf($id, $locale));

        return new JsonResponse([
            'ok' => true,
            'data' => $ancestors,
            'count' => count($ancestors),
            'node' => $this->summarizeNode($node),
            'locale' => $locale,
        ]);
    }

    #[Route('/api/category/list', name: 'api_category_list', methods: ['GET'])]
    public function list(Request $req): JsonResponse
    {
        $first = max(1, min(100, (int) $req->query->get('first', 20)));
        $depth = max(1, min(5, (int) $req->query->get('depth', 3)));
        $locale = (string) $req->query->get('locale', 'en');
        $taxonomy = (string) $req->query->get('taxonomy', 'catalog');
        $after = (string) $req->query->get('after', '');

        $rows = $this->repo->publishedTree($taxonomy, null, $depth, $locale);
        if ('' !== $after) {
            $cursor = base64_decode($after, true) ?: '';
            if ('' !== $cursor) {
                $rows = array_values(array_filter($rows, static fn (array $row): bool => (string) $row['path'] > $cursor));
            }
        }

        $slice = array_slice($rows, 0, $first);
        $next = '';
        if (count($rows) > $first && [] !== $slice) {
            $last = end($slice);
            $next = base64_encode((string) $last['path']);
        }

        return new JsonResponse([
            'ok' => true,
            'data' => array_values($slice),
            'count' => count($slice),
            'locale' => $locale,
            'taxonomy' => $taxonomy,
            'pageInfo' => [
                'after' => $next,
                'first' => $first,
                'depth' => $depth,
            ],
        ]);
    }

    private function summarizeNode(array $node): array
    {
        return [
            'id' => $node['id'],
            'name' => $node['name'],
            'slug' => $node['slug'],
            'path' => $node['path'],
        ];
    }
}
