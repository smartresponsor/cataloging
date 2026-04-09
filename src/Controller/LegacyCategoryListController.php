<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\ServiceInterface\CatalogReadServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
/**
 * Handles the legacy category list controller application flow.
 */
final class LegacyCategoryListController extends AbstractController
{
    /**
     * Initializes the legacy category list controller service collaborators.
     */
    public function __construct(private readonly CatalogReadServiceInterface $catalogReadService)
    {
    }
    /**
     * Executes the invokable workflow for this service.
     */
    #[Route('/category/list', name: 'legacy_category_list_handler', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $first = max(1, min(100, (int) $request->query->get('first', 20)));
        $after = (string) $request->query->get('after', '');
        $result = $this->catalogReadService->list($first, $after);
        $items = $result['item'];

        return $this->json([
            'items' => $items,
            'total' => [
                'value' => count($items),
                'accuracy' => 'exact',
            ],
            'pageInfo' => [
                'after' => $result['after'],
            ],
        ]);
    }
}
