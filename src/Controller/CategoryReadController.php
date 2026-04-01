<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Response\ApiResponseBuilder;
use App\ServiceInterface\CatalogReadServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryReadController extends AbstractController
{
    public function __construct(
        private readonly CatalogReadServiceInterface $categoryReadService,
        private readonly ApiResponseBuilder $responseBuilder,
    ) {
    }

    #[Route('/api/category/list', name: 'api_category_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $first = max(1, min(100, (int) $request->query->get('first', 20)));
        $after = (string) $request->query->get('after', '');
        $result = $this->categoryReadService->list($first, $after);

        $payload = $this->responseBuilder->success([
            'items' => $result['item'],
            'item' => $result['item'],
            'pageInfo' => ['after' => $result['after']],
        ]);

        return $this->json($this->stripStatus($payload));
    }

    #[Route('/api/category/{id}', name: 'api_category_by_id', methods: ['GET'], requirements: ['id' => '[0-9A-HJKMNP-TV-Za-hjkmnp-tv-z]{26}'])]
    public function byId(string $id): JsonResponse
    {
        $item = $this->categoryReadService->byId($id);
        if (null === $item) {
            $payload = $this->responseBuilder->error('not_found', ['not_found'], 404);

            return $this->json($this->stripStatus($payload), 404);
        }

        $payload = $this->responseBuilder->success(['item' => $item]);

        return $this->json($this->stripStatus($payload));
    }

    #[Route('/api/category/{id}/descendants', name: 'api_category_descendants_tree', methods: ['GET'], requirements: ['id' => '[0-9A-HJKMNP-TV-Za-hjkmnp-tv-z]{26}'])]
    public function descendantsTree(string $id): JsonResponse
    {
        $tree = $this->categoryReadService->descendantsTree($id);
        if (null === $tree) {
            $payload = $this->responseBuilder->error('not_found', ['not_found'], 404);

            return $this->json($this->stripStatus($payload), 404);
        }

        $payload = $this->responseBuilder->success(['item' => $tree]);

        return $this->json($this->stripStatus($payload));
    }

    #[Route('/api/category/{id}/child', name: 'api_category_child_list', methods: ['GET'], requirements: ['id' => '[0-9A-HJKMNP-TV-Za-hjkmnp-tv-z]{26}'])]
    public function childList(string $id): JsonResponse
    {
        $children = $this->categoryReadService->childList($id);
        if (null === $children) {
            $payload = $this->responseBuilder->error('not_found', ['not_found'], 404);

            return $this->json($this->stripStatus($payload), 404);
        }

        $payload = $this->responseBuilder->success([
            'items' => $children,
            'item' => $children,
        ]);

        return $this->json($this->stripStatus($payload));
    }

    #[Route('/api/category/{id}/ancestor', name: 'api_category_ancestor_list', methods: ['GET'], requirements: ['id' => '[0-9A-HJKMNP-TV-Za-hjkmnp-tv-z]{26}'])]
    public function ancestorList(string $id): JsonResponse
    {
        $ancestors = $this->categoryReadService->ancestorList($id);
        if (null === $ancestors) {
            $payload = $this->responseBuilder->error('not_found', ['not_found'], 404);

            return $this->json($this->stripStatus($payload), 404);
        }

        $payload = $this->responseBuilder->success([
            'items' => $ancestors,
            'item' => $ancestors,
        ]);

        return $this->json($this->stripStatus($payload));
    }

    /** @param array<string,mixed> $payload
     *  @return array<string,mixed>
     */
    private function stripStatus(array $payload): array
    {
        unset($payload['_status']);

        return $payload;
    }
}
