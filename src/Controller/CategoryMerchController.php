<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Service\CatalogMerchService;
use App\ValueObject\CategoryMerchBannerPublishRequest;
use App\ValueObject\CategoryMerchPinCreateRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Handles the category merch controller application flow.
 */
final class CategoryMerchController extends AbstractController
{
    /**
     * Initializes the category merch controller service collaborators.
     */
    public function __construct(private readonly CatalogMerchService $categoryMerchService)
    {
    }
    /**
     * Handles the pin create workflow.
     */
    #[Route('/api/category/{id}/pin', name: 'api_category_pin_create', methods: ['POST'])]
    #[IsGranted('category.merch')]
    public function pinCreate(string $id, Request $request): JsonResponse
    {
        $this->categoryMerchService->pinCreate(new CategoryMerchPinCreateRequest(
            $id,
            $this->bagString($request->request, 'recordId'),
            $this->bagInt($request->request, 'position'),
        ));

        return $this->json(['ok' => true]);
    }
    /**
     * Handles the pin delete workflow.
     */
    #[Route('/api/category/{id}/pin', name: 'api_category_pin_delete', methods: ['DELETE'])]
    #[IsGranted('category.merch')]
    public function pinDelete(string $id, Request $request): JsonResponse
    {
        $recordId = $this->bagString($request->query, 'recordId');
        $this->categoryMerchService->pinDelete($id, $recordId);

        return $this->json(['ok' => true]);
    }
    /**
     * Handles the order set workflow.
     */
    #[Route('/api/category/{id}/order', name: 'api_category_order_set', methods: ['POST'])]
    #[IsGranted('category.merch')]
    public function orderSet(string $id, Request $request): JsonResponse
    {
        $recordIds = $this->bagStringList($request->request, 'recordId');
        $this->categoryMerchService->orderSet($id, $recordIds);

        return $this->json(['ok' => true]);
    }
    /**
     * Handles the banner publish workflow.
     */
    #[Route('/api/category/{id}/banner/publish', name: 'api_category_banner_publish', methods: ['POST'])]
    #[IsGranted('category.merch')]
    public function bannerPublish(string $id, Request $request): JsonResponse
    {
        $bannerId = $this->categoryMerchService->bannerPublish(new CategoryMerchBannerPublishRequest(
            $id,
            $this->bagString($request->request, 'title'),
            $this->bagString($request->request, 'content'),
        ));

        return $this->json(['ok' => true, 'id' => $bannerId]);
    }
    /**
     * Handles the html publish workflow.
     */
    #[Route('/api/category/{id}/html/publish', name: 'api_category_html_publish', methods: ['POST'])]
    #[IsGranted('category.merch')]
    public function htmlPublish(string $id, Request $request): JsonResponse
    {
        $html = $this->bagString($request->request, 'html');
        $htmlBlockId = $this->categoryMerchService->htmlPublish($id, $html);

        return $this->json(['ok' => true, 'id' => $htmlBlockId]);
    }

    private function bagString(InputBag|ParameterBag $bag, string $key, string $default = ''): string
    {
        return $this->scalarString($bag->get($key, $default), $default);
    }

    private function bagInt(InputBag|ParameterBag $bag, string $key, int $default = 0): int
    {
        $value = $bag->get($key, $default);

        return is_numeric($value) ? (int) $value : $default;
    }

    /**
     * @param InputBag<array-key, mixed>|ParameterBag $bag
     *
     * @return list<string>
     */
    private function bagStringList(InputBag|ParameterBag $bag, string $key): array
    {
        $raw = $bag instanceof InputBag ? $bag->all($key) : $bag->all()[$key] ?? [];
        if (!is_array($raw)) {
            return [];
        }

        $items = [];

        foreach ($raw as $value) {
            $item = $this->scalarString($value);
            if ('' !== $item) {
                $items[] = $item;
            }
        }

        return array_values($items);
    }

    private function scalarString(mixed $value, string $default = ''): string
    {
        return is_scalar($value) ? (string) $value : $default;
    }
}
