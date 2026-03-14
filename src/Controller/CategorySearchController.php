<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Controller;

use App\Service\Query\Category\SearchService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CategorySearchController
{
    public function __construct(private readonly SearchService $search)
    {
    }

    #[Route('/api/catalog/search', name: 'api_category_search', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $q = (string) $request->query->get('q', '');
        $res = $this->search->search($q);

        return new JsonResponse($res);
    }
}
