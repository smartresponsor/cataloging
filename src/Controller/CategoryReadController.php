<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\ServiceInterface\CatalogReadServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryReadController extends AbstractController
{
    public function __construct(private readonly CatalogReadServiceInterface $categoryReadService)
    {
    }

    #[Route('/api/category/list', name: 'api_category_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $first = max(1, min(100, (int) $request->query->get('first', 20)));
        $after = (string) $request->query->get('after', '');
        $result = $this->categoryReadService->list($first, $after);

        return $this->json([
            'ok' => true,
            'items' => $result['item'],
            'item' => $result['item'],
            'pageInfo' => ['after' => $result['after']],
        ]);
    }

    #[Route('/api/category/{id}', name: 'api_category_by_id', methods: ['GET'], requirements: ['id' => '[0-9A-HJKMNP-TV-Za-hjkmnp-tv-z]{26}'])]
    public function byId(string $id): JsonResponse
    {
        $item = $this->categoryReadService->byId($id);
        if (null === $item) {
            return $this->json(['ok' => false, 'error' => 'not_found'], 404);
        }

        return $this->json(['ok' => true, 'item' => $item]);
    }

    #[Route('/api/category/{id}/descendants', name: 'api_category_descendants_tree', methods: ['GET'], requirements: ['id' => '[0-9A-HJKMNP-TV-Za-hjkmnp-tv-z]{26}'])]
    public function descendantsTree(string $id): JsonResponse
    {
        $tree = $this->categoryReadService->descendantsTree($id);
        if (null === $tree) {
            return $this->json(['ok' => false, 'error' => 'not_found'], 404);
        }

        return $this->json(['ok' => true, 'item' => $tree]);
    }

    #[Route('/api/category/{id}/child', name: 'api_category_child_list', methods: ['GET'])]
    public function childList(string $id): JsonResponse
    {
        $children = $this->categoryReadService->childList($id);
        if (null === $children) {
            return $this->json(['ok' => false, 'error' => 'not_found'], 404);
        }

        return $this->json([
            'ok' => true,
            'items' => $children,
            'item' => $children,
        ]);
    }

    #[Route('/api/category/{id}/ancestor', name: 'api_category_ancestor_list', methods: ['GET'])]
    public function ancestorList(string $id): JsonResponse
    {
        $ancestors = $this->categoryReadService->ancestorList($id);
        if (null === $ancestors) {
            return $this->json(['ok' => false, 'error' => 'not_found'], 404);
        }

        return $this->json([
            'ok' => true,
            'items' => $ancestors,
            'item' => $ancestors,
        ]);
    }
}
