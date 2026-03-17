<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Controller;

use App\Service\CollectionBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryCollectionController
{
    public function __construct(private readonly CollectionBuilder $builder)
    {
    }

    #[Route('/api/category/collection', name: 'api_category_collection', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $rules = json_decode($request->getContent(), true) ?? [];
        $all = [
            ['id' => 1, 'slug' => 'root', 'locale' => 'en', 'merchant' => 'default'],
            ['id' => 2, 'slug' => 'electronics', 'locale' => 'en', 'merchant' => 'default', 'tag' => 'featured'],
            ['id' => 3, 'slug' => 'ropa', 'locale' => 'es', 'merchant' => 'default'],
        ];
        $result = $this->builder->build($all, $rules);

        return new JsonResponse(['data' => $result]);
    }
}
