<?php

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
        $result = $this->search->search([
            'q' => $request->query->get('q'),
            'tenant' => $request->query->get('tenant'),
            'locale' => $request->query->get('locale'),
            'workflow_state' => $request->query->get('workflow_state'),
            'published' => $request->query->get('published'),
            'limit' => $request->query->get('limit'),
            'offset' => $request->query->get('offset'),
            'order' => $request->query->get('order'),
            'direction' => $request->query->get('direction'),
        ]);

        return new JsonResponse($result);
    }
}
