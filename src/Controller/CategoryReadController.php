<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller;

use App\Cataloging\ServiceInterface\CatalogReadServiceInterface;
use App\Cataloging\ValueObject\CategoryCatalogReadNodeRequest;
use App\Cataloging\ValueObject\CategoryCatalogReadPageRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles the category read controller application flow.
 */
final class CategoryReadController extends AbstractController
{
    /**
     * Initializes the category read controller service collaborators.
     */
    public function __construct(private readonly CatalogReadServiceInterface $categoryReadService)
    {
    }

    /**
     * Handles the list workflow.
     */
    #[Route('/api/category/list', name: 'api_category_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $result = $this->categoryReadService->list(new CategoryCatalogReadPageRequest(
            (int) $request->query->get('first', 20),
            (string) $request->query->get('after', ''),
        ));

        return $this->json([
            'ok' => true,
            'items' => $result['item'],
            'item' => $result['item'],
            'pageInfo' => ['after' => $result['after']],
        ]);
    }

    /**
     * Handles the by id workflow.
     */
    #[Route(
        '/api/category/{id}',
        name: 'api_category_by_id',
        requirements: ['id' => '[0-9A-HJKMNP-TV-Za-hjkmnp-tv-z]{26}'],
        methods: ['GET'],
    )]
    public function byId(string $id): JsonResponse
    {
        $item = $this->categoryReadService->byId(new CategoryCatalogReadNodeRequest($id));
        if (null === $item) {
            return $this->json(['ok' => false, 'error' => 'not_found'], 404);
        }

        return $this->json(['ok' => true, 'item' => $item]);
    }

    /**
     * Handles the descendants tree workflow.
     */
    #[Route(
        '/api/category/{id}/descendants',
        name: 'api_category_descendants_tree',
        requirements: ['id' => '[0-9A-HJKMNP-TV-Za-hjkmnp-tv-z]{26}'],
        methods: ['GET'],
    )]
    public function descendantsTree(string $id): JsonResponse
    {
        $tree = $this->categoryReadService->descendantsTree(new CategoryCatalogReadNodeRequest($id));
        if (null === $tree) {
            return $this->json(['ok' => false, 'error' => 'not_found'], 404);
        }

        return $this->json(['ok' => true, 'item' => $tree]);
    }

    /**
     * Handles the child list workflow.
     */
    #[Route('/api/category/{id}/child', name: 'api_category_child_list', methods: ['GET'])]
    public function childList(string $id): JsonResponse
    {
        $children = $this->categoryReadService->childList(new CategoryCatalogReadNodeRequest($id));
        if (null === $children) {
            return $this->json(['ok' => false, 'error' => 'not_found'], 404);
        }

        return $this->json([
            'ok' => true,
            'items' => $children,
            'item' => $children,
        ]);
    }

    /**
     * Handles the ancestor list workflow.
     */
    #[Route('/api/category/{id}/ancestor', name: 'api_category_ancestor_list', methods: ['GET'])]
    public function ancestorList(string $id): JsonResponse
    {
        $ancestors = $this->categoryReadService->ancestorList(new CategoryCatalogReadNodeRequest($id));
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
