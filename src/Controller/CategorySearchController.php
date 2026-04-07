<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\SearchService;
use App\ServiceInterface\CategoryReadScopeServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Search delivery adapter over shared search and tenant-scope services.
 */
final class CategorySearchController
{
    public function __construct(
        private readonly SearchService $search,
        private readonly CategoryReadScopeServiceInterface $categoryReadScopeService,
    ) {
    }

    #[Route('/api/category/search', name: 'api_category_search', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $criteria = [
                'q' => $request->query->get('q'),
                'tenant' => $request->query->get('tenant'),
                'locale' => $request->query->get('locale'),
                'workflow_state' => $request->query->get('workflow_state'),
                'published' => $request->query->get('published'),
                'limit' => $request->query->get('limit'),
                'offset' => $request->query->get('offset'),
                'order' => $request->query->get('order'),
                'direction' => $request->query->get('direction'),
            ];

            $criteria = $this->categoryReadScopeService->applyTenantScope($request, $criteria);
            $result = $this->search->search($criteria);

            return new JsonResponse($result);
        } catch (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 403);
        }
    }

}
