<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Request\CategoryRulePreviewRequest;
use App\Service\CatalogRuleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CategoryRuleController extends AbstractController
{
    public function __construct(private readonly CatalogRuleService $categoryRuleService)
    {
    }

    #[Route('/api/category/virtual/preview', name: 'api_category_virtual_preview', methods: ['POST'])]
    #[IsGranted('category.rule')]
    public function preview(Request $request): JsonResponse
    {
        $input = CategoryRulePreviewRequest::fromJson((string) $request->getContent());
        if (!$input->isValid()) {
            return $this->json(['ok' => false, 'error' => 'bad_spec'], 400);
        }

        $preview = $this->categoryRuleService->preview($input->spec ?? []);
        if (null === $preview) {
            return $this->json(['ok' => false, 'error' => 'bad_spec'], 400);
        }

        $limit = (int) ($_ENV['RULE_MAX_CARDINALITY'] ?? 100000);
        if ($preview['count'] > $limit) {
            return $this->json([
                'ok' => false,
                'error' => 'cardinality_exceeds',
                'limit' => $limit,
                'count' => $preview['count'],
            ], 413);
        }

        return $this->json(['ok' => true, 'item' => $preview]);
    }

    #[Route('/api/category/virtual/apply/{id}', name: 'api_category_virtual_apply', methods: ['POST'])]
    #[IsGranted('category.rule')]
    public function apply(string $id): JsonResponse
    {
        if (!$this->categoryRuleService->apply($id)) {
            return $this->json(['ok' => false, 'error' => 'not_found'], 404);
        }

        return $this->json(['ok' => true]);
    }
}
