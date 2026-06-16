<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller\Catalog;

use App\Cataloging\Service\Catalog\CatalogSurfaceContractFactory;
use App\Cataloging\ServiceInterface\CatalogCategoryProjectionReadServiceInterface;
use App\Cataloging\ServiceInterface\CatalogCategoryReadScopeServiceInterface;
use App\Cataloging\ValueObject\CategoryProjectionCriteria;
use App\Cataloging\ValueObject\CategoryReadScopeRequest;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Storefront delivery adapter over shared projection/read scope services.
 */
final readonly class CatalogCategoryStorefrontController
{
    /**
     * Initializes the category storefront controller service collaborators.
     */
    public function __construct(
        private CatalogCategoryProjectionReadServiceInterface $categoryProjectionReadService,
        private CatalogCategoryReadScopeServiceInterface $categoryReadScopeService,
        private CatalogSurfaceContractFactory $surfaceContractFactory,
    ) {
    }

    #[Route('/catalog/', name: 'cataloging_catalog_index', methods: ['GET'])]
    #[Route('/catalog', name: 'cataloging_catalog_index_no_slash', methods: ['GET'])]
    #[Route('/catalog/index', name: 'cataloging_catalog_index_legacy', methods: ['GET'])]
    public function show(Request $request): mixed
    {
        $categories = [];

        try {
            $criteria = $this->categoryReadScopeService->applyTenantScope(new CategoryReadScopeRequest(
                $request,
                CategoryProjectionCriteria::fromArray([
                    'q' => $request->query->get('q'),
                    'tenant' => $request->query->get('tenant'),
                    'locale' => $request->query->get('locale'),
                    'workflow_state' => $request->query->get('workflow_state'),
                    'published' => $request->query->has('published') ? $request->query->getBoolean('published') : null,
                    'limit' => $request->query->get('limit') ?? 200,
                    'offset' => $request->query->get('offset') ?? 0,
                    'order' => $request->query->get('order') ?? 'nameEntity',
                    'direction' => $request->query->get('direction') ?? 'asc',
                ]),
            ));

            $categories = $this->categoryProjectionReadService->list($criteria);
        } catch (AccessDeniedHttpException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 403);
        } catch (Exception) {
            $categories = [];
        }

        return $this->surfaceContractFactory->create(
            'catalog',
            $categories,
            [
                'tenant' => $request->query->get('tenant'),
                'locale' => $request->query->get('locale'),
                'workflow_state' => $request->query->get('workflow_state'),
            ],
            (string) $request->query->get('q', ''),
        );
    }

    /**
     * Executes the invokable workflow for this service.
     */
    #[Route('/api/catalog/category/storefront', name: 'api_category_storefront', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $criteria = $this->categoryReadScopeService->applyTenantScope(new CategoryReadScopeRequest(
                $request,
                CategoryProjectionCriteria::fromArray([
                    'tenant' => $request->query->get('tenant'),
                    'locale' => $request->query->get('locale'),
                    'workflow_state' => $request->query->get('workflow_state'),
                    'published' => $request->query->get('published') ?? true,
                    'limit' => $request->query->get('limit') ?? 200,
                    'offset' => $request->query->get('offset') ?? 0,
                    'order' => $request->query->get('order') ?? 'nameEntity',
                    'direction' => $request->query->get('direction') ?? 'asc',
                ]),
            ));

            return new JsonResponse($this->categoryProjectionReadService->list($criteria));
        } catch (AccessDeniedHttpException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 403);
        } catch (Exception) {
            return new JsonResponse(['error' => 'Unable to read storefront categories.'], 500);
        }
    }
}
