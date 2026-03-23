<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Service\CategoryReadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryReadController extends AbstractController
{
    public function __construct(private readonly CategoryReadService $categoryReadService)
    {
    }

    #[Route('/api/category/{id}/child', name: 'api_category_child_list', methods: ['GET'])]
    public function childList(string $id): JsonResponse
    {
        $children = $this->categoryReadService->childList($id);
        if (null === $children) {
            return $this->json(['ok' => false, 'error' => 'not_found'], 404);
        }

        return $this->json(['ok' => true, 'item' => $children]);
    }

    #[Route('/api/category/{id}/ancestor', name: 'api_category_ancestor_list', methods: ['GET'])]
    public function ancestorList(string $id): JsonResponse
    {
        $ancestors = $this->categoryReadService->ancestorList($id);
        if (null === $ancestors) {
            return $this->json(['ok' => false, 'error' => 'not_found'], 404);
        }

        return $this->json(['ok' => true, 'item' => $ancestors]);
    }

    #[Route('/api/category/list', name: 'api_category_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $first = max(1, min(100, (int) $request->query->get('first', 20)));
        $after = (string) $request->query->get('after', '');
        $result = $this->categoryReadService->list($first, $after);

        return $this->json([
            'ok' => true,
            'item' => $result['item'],
            'pageInfo' => ['after' => $result['after']],
        ]);
    }
}
