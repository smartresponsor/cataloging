<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Service\CategoryMerchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CategoryMerchController extends AbstractController
{
    public function __construct(private readonly CategoryMerchService $categoryMerchService)
    {
    }

    #[Route('/api/category/{id}/pin', name: 'api_category_pin_create', methods: ['POST'])]
    #[IsGranted('category.merch')]
    public function pinCreate(string $id, Request $request): JsonResponse
    {
        $recordId = (string) $request->request->get('recordId');
        $position = (int) $request->request->get('position', 0);
        $this->categoryMerchService->pinCreate($id, $recordId, $position);

        return $this->json(['ok' => true]);
    }

    #[Route('/api/category/{id}/pin', name: 'api_category_pin_delete', methods: ['DELETE'])]
    #[IsGranted('category.merch')]
    public function pinDelete(string $id, Request $request): JsonResponse
    {
        $recordId = (string) $request->query->get('recordId');
        $this->categoryMerchService->pinDelete($id, $recordId);

        return $this->json(['ok' => true]);
    }

    #[Route('/api/category/{id}/order', name: 'api_category_order_set', methods: ['POST'])]
    #[IsGranted('category.merch')]
    public function orderSet(string $id, Request $request): JsonResponse
    {
        $recordIds = $request->request->all('recordId');
        $this->categoryMerchService->orderSet($id, $recordIds);

        return $this->json(['ok' => true]);
    }

    #[Route('/api/category/{id}/banner/publish', name: 'api_category_banner_publish', methods: ['POST'])]
    #[IsGranted('category.merch')]
    public function bannerPublish(string $id, Request $request): JsonResponse
    {
        $title = (string) $request->request->get('title');
        $content = (string) $request->request->get('content');
        $bannerId = $this->categoryMerchService->bannerPublish($id, $title, $content);

        return $this->json(['ok' => true, 'id' => $bannerId]);
    }

    #[Route('/api/category/{id}/html/publish', name: 'api_category_html_publish', methods: ['POST'])]
    #[IsGranted('category.merch')]
    public function htmlPublish(string $id, Request $request): JsonResponse
    {
        $html = (string) $request->request->get('html');
        $htmlBlockId = $this->categoryMerchService->htmlPublish($id, $html);

        return $this->json(['ok' => true, 'id' => $htmlBlockId]);
    }
}
