<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Request\CatalogCategoryMoveRequest;
use App\Request\CatalogCategoryPublishRequest;
use App\Service\CatalogCategoryMutationRequestContextResolver;
use App\Service\CatalogCategoryMutationAuthorizationService;
use App\ServiceInterface\CatalogCategoryMutationServiceInterface;
use App\ServiceInterface\CategoryProjectionReadServiceInterface;
use App\ServiceInterface\CategoryReadScopeServiceInterface;
use App\ValueObject\CatalogCategoryMutationMoveRequest;
use App\ValueObject\CatalogCategoryMutationPolicy;
use App\ValueObject\CatalogCategoryMutationPublishRequest;
use App\ValueObject\CategoryProjectionCriteria;
use App\ValueObject\CategoryReadScopeRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles the catalog category api controller application flow.
 */
final readonly class CatalogCategoryApiController
{
    /**
     * Initializes the category api controller service collaborators.
     */
    public function __construct(
        private CatalogCategoryMutationServiceInterface $categoryMutationService,
        private CatalogCategoryMutationAuthorizationService $categoryMutationAuthorizationService,
        private CategoryProjectionReadServiceInterface $categoryProjectionReadService,
        private CategoryReadScopeServiceInterface $categoryReadScopeService,
        private CatalogCategoryMutationRequestContextResolver $requestContextResolver,
    ) {
    }

    /**
     * Handles the tree workflow.
     */
    #[Route('/api/catalog-category/tree', name: 'api_catalog_category_tree', methods: ['GET'])]
    public function tree(Request $request): JsonResponse
    {
        $criteria = $this->categoryReadScopeService->applyTenantScope(new CategoryReadScopeRequest(
            $request,
            CategoryProjectionCriteria::fromArray([
                'tenant' => $request->query->get('tenant'),
                'locale' => $request->query->get('locale'),
                'workflow_state' => $request->query->get('workflow_state'),
                'published' => $request->query->get('published'),
            ]),
        ));

        return new JsonResponse(['data' => $this->categoryProjectionReadService->tree($criteria)]);
    }

    /**
     * Handles the move workflow.
     */
    #[Route('/api/catalog-category/{id}/move', name: 'api_catalog_category_move', methods: ['POST'])]
    public function move(string $id, Request $request): JsonResponse
    {
        $dto = CatalogCategoryMoveRequest::fromArray($this->decodeMap($request));
        if (!$dto->isValid()) {
            return new JsonResponse(['error' => $dto->getErrors()], 400);
        }

        $this->categoryMutationAuthorizationService->assertCanMove($id);

        $result = $this->categoryMutationService->move(new CatalogCategoryMutationMoveRequest(
            $id,
            (string) $dto->parentId,
            $this->requestContextResolver->actorId($request),
            $dto->treeId,
            CatalogCategoryMutationPolicy::fromString($dto->policy),
            $dto->dryRun,
            $dto->locale,
            $this->requestContextResolver->idempotencyKey($request),
            $this->requestContextResolver->correlationId($request),
        ));

        return new JsonResponse(['data' => $result->toArray()], 200);
    }

    /**
     * Handles the publish workflow.
     */
    #[Route('/api/catalog-category/{id}/publish', name: 'api_catalog_category_publish', methods: ['POST'])]
    public function publish(string $id, Request $request): JsonResponse
    {
        $dto = CatalogCategoryPublishRequest::fromArray($this->decodeMap($request));
        if (!$dto->isValid()) {
            return new JsonResponse(['error' => $dto->getErrors()], 400);
        }

        $this->categoryMutationAuthorizationService->assertCanPublish($id);

        $result = $this->categoryMutationService->publish(new CatalogCategoryMutationPublishRequest(
            $id,
            (bool) $dto->published,
            $dto->checks,
            $this->requestContextResolver->actorId($request),
            $dto->reason,
            $this->requestContextResolver->idempotencyKey($request),
            $this->requestContextResolver->correlationId($request),
        ));

        return new JsonResponse(['data' => $result->toArray()], 200);
    }

    /** @return array<string,mixed> */
    private function decodeMap(Request $request): array
    {
        $decoded = json_decode($request->getContent(), true);

        return is_array($decoded) ? $decoded : [];
    }
}
