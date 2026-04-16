<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Request\CategoryRulePreviewRequest;
use App\Service\CatalogRuleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Handles the category rule controller application flow.
 */
final class CategoryRuleController extends AbstractController
{
    /**
     * Initializes the category rule controller service collaborators.
     */
    public function __construct(private readonly CatalogRuleService $categoryRuleService)
    {
    }

    /**
     * Handles the preview workflow.
     */
    #[Route('/api/category/virtual/preview', name: 'api_category_virtual_preview', methods: ['POST'])]
    #[IsGranted('category.rule')]
    public function preview(Request $request): JsonResponse
    {
        try {
            $input = CategoryRulePreviewRequest::fromJson((string) $request->getContent());
            if (!$input->isValid()) {
                return $this->json(['ok' => false, 'error' => 'bad_spec'], 400);
            }

            $preview = $this->categoryRuleService->preview($input->spec ?? []);
            if (null === $preview) {
                return $this->json(['ok' => false, 'error' => 'bad_spec'], 400);
            }

            $limitValue = $_ENV['RULE_MAX_CARDINALITY'] ?? 100000;
            $limit = is_numeric($limitValue) ? (int) $limitValue : 100000;
            if ($preview['count'] > $limit) {
                return $this->json([
                    'ok' => false,
                    'error' => 'cardinality_exceeds',
                    'limit' => $limit,
                    'count' => $preview['count'],
                ], 413);
            }

            return $this->json(['ok' => true, 'item' => $preview]);
        } catch (\Throwable) {
            return $this->json(['ok' => false, 'error' => 'rule_preview_failed'], 500);
        }
    }

    /**
     * Handles the apply workflow.
     */
    #[Route('/api/category/virtual/apply/{id}', name: 'api_category_virtual_apply', methods: ['POST'])]
    #[IsGranted('category.rule')]
    public function apply(string $id): JsonResponse
    {
        try {
            if (!$this->categoryRuleService->apply($id)) {
                return $this->json(['ok' => false, 'error' => 'not_found'], 404);
            }
        } catch (\Throwable) {
            return $this->json(['ok' => false, 'error' => 'rule_apply_failed'], 500);
        }

        return $this->json(['ok' => true]);
    }
}
