<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller\Catalog;

use App\Cataloging\Request\MoveCategoryRequest;
use App\Cataloging\Request\PublishCategoryRequest;
use App\Cataloging\Service\CatalogCategoryMutationAuthorizationService;
use App\Cataloging\Service\CategoryMutationRequestContextResolver;
use App\Cataloging\Service\CategoryPayloadValueNormalizer;
use App\Cataloging\ServiceInterface\CatalogCategoryMutationServiceInterface;
use App\Cataloging\ServiceInterface\CatalogCategoryProjectionReadServiceInterface;
use App\Cataloging\ServiceInterface\CatalogCategoryReadScopeServiceInterface;
use App\Cataloging\ValueObject\CategoryMutationMoveRequest;
use App\Cataloging\ValueObject\CategoryMutationPublishRequest;
use App\Cataloging\ValueObject\CategoryProjectionCriteria;
use App\Cataloging\ValueObject\CategoryReadScopeRequest;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles the category api controller application flow.
 */
final readonly class CatalogCategoryApiController
{
    /**
     * Initializes the category api controller service collaborators.
     */
    public function __construct(
        private CatalogCategoryMutationServiceInterface $categoryMutationService,
        private CatalogCategoryMutationAuthorizationService $categoryMutationAuthorizationService,
        private CatalogCategoryProjectionReadServiceInterface $categoryProjectionReadService,
        private CatalogCategoryReadScopeServiceInterface $categoryReadScopeService,
        private CategoryMutationRequestContextResolver $requestContextResolver,
    ) {
    }

    /**
     * Handles the tree workflow.
     */
    #[Route('/api/category/tree', name: 'api_category_tree', methods: ['GET'])]
    public function tree(Request $request): JsonResponse
    {
        try {
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
        } catch (AccessDeniedHttpException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 403);
        } catch (Exception) {
            return new JsonResponse(['error' => 'Unable to read category tree.'], 500);
        }
    }

    /**
     * Handles the move workflow.
     */
    #[Route('/api/category/{id}/move', name: 'api_category_move', methods: ['POST'])]
    public function move(string $id, Request $request): JsonResponse
    {
        $dto = MoveCategoryRequest::fromArray($this->decodeMap($request));
        if (!$dto->isValid()) {
            return new JsonResponse(['error' => $dto->getErrors()], 400);
        }

        $this->categoryMutationAuthorizationService->assertCanMove($id);

        $result = $this->categoryMutationService->move(new CategoryMutationMoveRequest(
            $id,
            (string) $dto->parentId,
            $this->requestContextResolver->actorId($request),
            $dto->treeId,
            $dto->policy,
            $dto->dryRun,
            $dto->locale,
            $this->requestContextResolver->idempotencyKey($request),
            $this->requestContextResolver->correlationId($request),
        ));

        return new JsonResponse(['data' => $result], 200);
    }

    /**
     * Handles the publish workflow.
     */
    #[Route('/api/category/{id}/publish', name: 'api_category_publish', methods: ['POST'])]
    public function publish(string $id, Request $request): JsonResponse
    {
        $dto = PublishCategoryRequest::fromArray($this->decodeMap($request));
        if (!$dto->isValid()) {
            return new JsonResponse(['error' => $dto->getErrors()], 400);
        }

        $this->categoryMutationAuthorizationService->assertCanPublish($id);

        $result = $this->categoryMutationService->publish(new CategoryMutationPublishRequest(
            $id,
            (bool) $dto->published,
            $dto->checks,
            $this->requestContextResolver->actorId($request),
            $dto->reason,
            $this->requestContextResolver->idempotencyKey($request),
            $this->requestContextResolver->correlationId($request),
        ));

        return new JsonResponse(['data' => $result], 200);
    }

    /** @return array<string,mixed> */
    private function decodeMap(Request $request): array
    {
        return CategoryPayloadValueNormalizer::nestedMap(json_decode($request->getContent(), true));
    }
}
