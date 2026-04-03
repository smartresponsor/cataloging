<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Request\MoveCategoryRequest;
use App\Request\PublishCategoryRequest;
use App\Service\CategoryMutationAuthorizationService;
use App\ServiceInterface\CategoryMutationServiceInterface;
use App\ServiceInterface\CategoryProjectionReadServiceInterface;
use App\ServiceInterface\CategoryReadScopeServiceInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

final class CategoryApiController
{
    public function __construct(
        private readonly CategoryMutationServiceInterface $categoryMutationService,
        private readonly CategoryMutationAuthorizationService $categoryMutationAuthorizationService,
        private readonly CategoryProjectionReadServiceInterface $categoryProjectionReadService,
        private readonly CategoryReadScopeServiceInterface $categoryReadScopeService,
        private readonly Security $security,
    ) {
    }

    #[Route('/api/category/tree', name: 'api_category_tree', methods: ['GET'])]
    public function tree(Request $request): JsonResponse
    {
        try {
            $criteria = [
                'tenant' => $request->query->get('tenant'),
                'locale' => $request->query->get('locale'),
                'workflow_state' => $request->query->get('workflow_state'),
                'published' => $request->query->get('published'),
            ];
            $criteria = $this->categoryReadScopeService->applyTenantScope($request, $criteria);

            return new JsonResponse(['data' => $this->categoryProjectionReadService->tree($criteria)]);
        } catch (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 403);
        }
    }

    #[Route('/api/category/{id}/move', name: 'api_category_move', methods: ['POST'])]
    public function move(string $id, Request $request): JsonResponse
    {
        $dto = MoveCategoryRequest::fromArray($this->decodeMap($request));
        if (!$dto->isValid()) {
            return new JsonResponse(['error' => $dto->getErrors()], 400);
        }

        try {
            $this->categoryMutationAuthorizationService->assertCanMove($id);

            $result = $this->categoryMutationService->move(
                $id,
                (string) $dto->parentId,
                $this->resolveActorId($request),
                $dto->treeId,
                $dto->policy,
                $dto->dryRun,
                $dto->locale,
                $this->resolveIdempotencyKey($request),
                $this->resolveCorrelationId($request),
            );

            return new JsonResponse(['data' => $result], 200);
        } catch (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 403);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 400);
        } catch (\DomainException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 409);
        } catch (\RuntimeException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], str_contains($exception->getMessage(), 'was not found') ? 404 : 409);
        }
    }

    #[Route('/api/category/{id}/publish', name: 'api_category_publish', methods: ['POST'])]
    public function publish(string $id, Request $request): JsonResponse
    {
        $dto = PublishCategoryRequest::fromArray($this->decodeMap($request));
        if (!$dto->isValid()) {
            return new JsonResponse(['error' => $dto->getErrors()], 400);
        }

        try {
            $this->categoryMutationAuthorizationService->assertCanPublish($id);

            $result = $this->categoryMutationService->publish(
                $id,
                (bool) $dto->published,
                $dto->checks,
                $this->resolveActorId($request),
                $dto->reason,
                $this->resolveIdempotencyKey($request),
                $this->resolveCorrelationId($request),
            );

            return new JsonResponse(['data' => $result], 200);
        } catch (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 403);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 400);
        } catch (\DomainException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 409);
        } catch (\RuntimeException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], str_contains($exception->getMessage(), 'was not found') ? 404 : 409);
        }
    }

    /** @return array<string,mixed> */
    private function decodeMap(Request $request): array
    {
        $decoded = json_decode($request->getContent(), true);

        return is_array($decoded) ? $decoded : [];
    }

    private function resolveIdempotencyKey(Request $request): ?string
    {
        $headerValue = trim((string) $request->headers->get('X-Idempotency-Key', ''));

        return '' !== $headerValue ? $headerValue : null;
    }

    private function resolveCorrelationId(Request $request): ?string
    {
        $headerValue = trim((string) $request->headers->get('X-Correlation-ID', ''));

        return '' !== $headerValue ? $headerValue : null;
    }

    private function resolveActorId(Request $request): string
    {
        $user = $this->security->getUser();
        if ($user instanceof UserInterface) {
            $identifier = trim($user->getUserIdentifier());
            if ('' !== $identifier) {
                return $identifier;
            }
        }

        $headerActorId = trim((string) $request->headers->get('X-Actor-Id', ''));
        if ('' !== $headerActorId) {
            return $headerActorId;
        }

        return 'category-api';
    }
}
