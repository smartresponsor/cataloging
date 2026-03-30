<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Service\SearchService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CategorySearchController
{
    public function __construct(private readonly SearchService $search)
    {
    }

    #[Route('/api/category/search', name: 'api_category_search', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $q = (string) $request->query->get('q', '');
        $res = $this->search->search($q);

        return new JsonResponse($res);
    }
}
