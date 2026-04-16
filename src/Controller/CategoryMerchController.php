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
     *
     * @throws \Throwable
     */
    #[Route('/api/category/{id}/pin', name: 'api_category_pin_create', methods: ['POST'])]
    #[IsGranted('category.merch')]
    public function pinCreate(string $id, Request $request): JsonResponse
    {
        $this->categoryMerchService->pinCreate($this->pinCreateRequest($id, $request));

        return $this->json(['ok' => true]);
    }

    /**
     * Handles the pin delete workflow.
     *
     * @throws \Throwable
     */
    #[Route('/api/category/{id}/pin', name: 'api_category_pin_delete', methods: ['DELETE'])]
    #[IsGranted('category.merch')]
    public function pinDelete(string $id, Request $request): JsonResponse
    {
        $recordId = $this->queryRecordId($request);
        $this->categoryMerchService->pinDelete($id, $recordId);

        return $this->json(['ok' => true]);
    }

    /**
     * Handles the order set workflow.
     *
     * @throws \Throwable
     */
    #[Route('/api/category/{id}/order', name: 'api_category_order_set', methods: ['POST'])]
    #[IsGranted('category.merch')]
    public function orderSet(string $id, Request $request): JsonResponse
    {
        $recordIds = $this->orderRecordIds($request);
        $this->categoryMerchService->orderSet($id, $recordIds);

        return $this->json(['ok' => true]);
    }

    /**
     * Handles the banner publish workflow.
     *
     * @throws \Throwable
     */
    #[Route('/api/category/{id}/banner/publish', name: 'api_category_banner_publish', methods: ['POST'])]
    #[IsGranted('category.merch')]
    public function bannerPublish(string $id, Request $request): JsonResponse
    {
        $bannerId = $this->categoryMerchService->bannerPublish(new CategoryMerchBannerPublishRequest(
            $id,
            $this->requestTitle($request),
            $this->requestContent($request),
        ));

        return $this->json(['ok' => true, 'id' => $bannerId]);
    }

    /**
     * Handles the html publish workflow.
     *
     * @throws \Throwable
     */
    #[Route('/api/category/{id}/html/publish', name: 'api_category_html_publish', methods: ['POST'])]
    #[IsGranted('category.merch')]
    public function htmlPublish(string $id, Request $request): JsonResponse
    {
        $html = $this->requestHtml($request);
        $htmlBlockId = $this->categoryMerchService->htmlPublish($id, $html);

        return $this->json(['ok' => true, 'id' => $htmlBlockId]);
    }

    private function pinCreateRequest(string $categoryId, Request $request): CategoryMerchPinCreateRequest
    {
        return new CategoryMerchPinCreateRequest(
            $categoryId,
            $this->requestRecordId($request),
            $this->requestPosition($request),
        );
    }

    /** @return list<string> */
    private function orderRecordIds(Request $request): array
    {
        return $this->requestRecordIds($request);
    }

    private function queryRecordId(Request $request): string
    {
        return $this->recordIdFromBag($request->query);
    }

    private function requestRecordId(Request $request): string
    {
        return $this->recordIdFromBag($request->request);
    }

    private function requestTitle(Request $request): string
    {
        return $this->titleFromBag($request->request);
    }

    private function requestContent(Request $request): string
    {
        return $this->contentFromBag($request->request);
    }

    private function requestHtml(Request $request): string
    {
        return $this->htmlFromBag($request->request);
    }

    private function requestPosition(Request $request): int
    {
        return $this->positionFromBag($request->request);
    }

    /** @return list<string> */
    private function requestRecordIds(Request $request): array
    {
        return $this->recordIdsFromBag($request->request);
    }

    private function recordIdFromBag(InputBag|ParameterBag $bag): string
    {
        return $this->scalarString($bag->get('recordId'));
    }

    private function titleFromBag(InputBag|ParameterBag $bag): string
    {
        return $this->scalarString($bag->get('title'));
    }

    private function contentFromBag(InputBag|ParameterBag $bag): string
    {
        return $this->scalarString($bag->get('content'));
    }

    private function htmlFromBag(InputBag|ParameterBag $bag): string
    {
        return $this->scalarString($bag->get('html'));
    }

    private function positionFromBag(InputBag|ParameterBag $bag): int
    {
        $value = $bag->get('position');

        return is_numeric($value) ? (int) $value : 0;
    }

    /**
     * @param InputBag<array-key, mixed>|ParameterBag $bag
     *
     * @return list<string>
     */
    private function recordIdsFromBag(InputBag|ParameterBag $bag): array
    {
        $raw = $bag instanceof InputBag ? $bag->all('recordId') : $bag->all()['recordId'] ?? [];
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

        return $items;
    }

    private function scalarString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
