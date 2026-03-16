<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Controller;

use App\Request\MovetestsRequest;
use App\Request\PublishtestsRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class testsApiController
{
    #[Route('/api/category/tree', name: 'api_category_tree', methods: ['GET'])]
    public function tree(): JsonResponse
    {
        return new JsonResponse(['data' => [['id' => 1, 'name' => 'Root']]]);
    }

    #[Route('/api/category/{id}/move', name: 'api_category_move', methods: ['POST'])]
    public function move(string $id, Request $request): JsonResponse
    {
        $dto = MovetestsRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        if (!$dto->isValid()) {
            return new JsonResponse(['error' => $dto->getErrors()], 400);
        }

        return new JsonResponse(['status' => 'ok']);
    }

    #[Route('/api/category/{id}/publish', name: 'api_category_publish', methods: ['POST'])]
    public function publish(string $id, Request $request): JsonResponse
    {
        $dto = PublishtestsRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        if (!$dto->isValid()) {
            return new JsonResponse(['error' => $dto->getErrors()], 400);
        }

        return new JsonResponse(['status' => 'ok']);
    }
}
