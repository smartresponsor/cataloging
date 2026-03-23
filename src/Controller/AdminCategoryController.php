<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Service\CategoryMovePreviewService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class AdminCategoryController extends AbstractController
{
    public function __construct(private readonly CategoryMovePreviewService $categoryMovePreviewService)
    {
    }

    #[Route('/admin/category/preview-move', name: 'admin_category_preview_move', methods: ['POST'])]
    public function previewMove(Request $request): JsonResponse
    {
        $sourceId = (string) $request->request->get('sourceId');
        $targetParentId = (string) $request->request->get('targetParentId');
        $preview = $this->categoryMovePreviewService->preview($sourceId, $targetParentId);

        if (null === $preview) {
            return $this->json(['ok' => false, 'error' => 'not_found'], 404);
        }

        return $this->json([
            'ok' => true,
            'preview' => $preview,
        ]);
    }
}
