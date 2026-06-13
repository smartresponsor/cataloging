<?php

declare(strict_types=1);

namespace App\Cataloging\Controller\Catalog;

use App\Cataloging\Service\CatalogSearchService;
use App\Cataloging\ServiceInterface\CatalogCategoryReadScopeServiceInterface;
use App\Cataloging\ValueObject\CategoryProjectionCriteria;
use App\Cataloging\ValueObject\CategoryReadScopeRequest;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Search delivery adapter over shared search and tenant-scope services.
 */
final readonly class CatalogCategorySearchController
{
    /**
     * Initializes the category search controller service collaborators.
     */
    public function __construct(
        private CatalogSearchService $search,
        private CatalogCategoryReadScopeServiceInterface $categoryReadScopeService,
    ) {
    }

    /**
     * Executes the invokable workflow for this service.
     */
    #[Route('/api/catalog/category/search', name: 'api_category_search', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $criteria = $this->categoryReadScopeService->applyTenantScope(new CategoryReadScopeRequest(
                $request,
                CategoryProjectionCriteria::fromArray([
                    'q' => $request->query->get('q'),
                    'tenant' => $request->query->get('tenant'),
                    'locale' => $request->query->get('locale'),
                    'workflow_state' => $request->query->get('workflow_state'),
                    'published' => $request->query->get('published'),
                    'limit' => $request->query->get('limit'),
                    'offset' => $request->query->get('offset'),
                    'order' => $request->query->get('order'),
                    'direction' => $request->query->get('direction'),
                ]),
            ));
            $result = $this->search->search($criteria);

            return new JsonResponse($result);
        } catch (AccessDeniedHttpException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 403);
        } catch (Exception $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 500);
        }
    }
}
