<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Request\CategoryCollectionRequest;
use App\Service\CatalogCollectionService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryCollectionController
{
    public function __construct(private readonly CatalogCollectionService $service)
    {
    }

    #[Route('/api/category/collection', name: 'api_category_collection', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $input = CategoryCollectionRequest::fromJson((string) $request->getContent());
        if (!$input->isValid()) {
            return new JsonResponse(['ok' => false, 'error' => 'validation_failed', 'errors' => $input->getErrors()], 400);
        }

        $result = $this->service->build($this->normalizeRules($input->rules));

        return new JsonResponse([
            'ok' => true,
            'items' => $result,
            'data' => $result,
            'total' => count($result),
        ]);
    }

    /**
     * @param array<mixed> $rules
     *
     * @return array<string, array<int, bool|float|int|string>|bool|float|int|string>
     */
    private function normalizeRules(array $rules): array
    {
        $normalized = [];
        foreach ($rules as $key => $value) {
            if (!is_string($key)) {
                continue;
            }
            if (is_bool($value) || is_float($value) || is_int($value) || is_string($value)) {
                $normalized[$key] = $value;
                continue;
            }
            if (!is_array($value)) {
                continue;
            }
            $items = [];
            foreach ($value as $item) {
                if (is_bool($item) || is_float($item) || is_int($item) || is_string($item)) {
                    $items[] = $item;
                }
            }
            $normalized[$key] = $items;
        }

        return $normalized;
    }
}
