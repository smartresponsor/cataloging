<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Request\CategoryAttachmentAddRequest;
use App\Service\AttachmentService;
use App\Service\CategoryAttachmentAuthorizationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
/**
 * Handles the category attachment controller application flow.
 */
final class CategoryAttachmentController
{
    /**
     * Initializes the category attachment controller service collaborators.
     */
    public function __construct(
        private readonly AttachmentService $service,
        private readonly CategoryAttachmentAuthorizationService $authorizationService,
    )
    {
    }
    /**
     * Handles the list workflow.
     */
    #[Route('/api/category/attachment', name: 'api_category_attachment_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $categoryId = $request->query->get('category_id');
        $normalizedCategoryId = is_string($categoryId) ? trim($categoryId) : '';

        try {
            $this->authorizationService->assertCanList('' !== $normalizedCategoryId ? $normalizedCategoryId : null);

            return new JsonResponse([
                'ok' => true,
                'items' => $this->service->list('' !== $normalizedCategoryId ? $normalizedCategoryId : null),
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $exception) {
            return new JsonResponse(['ok' => false, 'errors' => [$exception->getMessage()]], 403);
        }
    }
    /**
     * Handles the add workflow.
     */
    #[Route('/api/category/attachment', name: 'api_category_attachment_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $input = CategoryAttachmentAddRequest::fromJson((string) $request->getContent());
        if (!$input->isValid()) {
            return new JsonResponse(['ok' => false, 'errors' => $input->getErrors()], 400);
        }

        try {
            $this->authorizationService->assertCanAttach($input->categoryId ?? '');

            $item = $this->service->add(
                $input->categoryId ?? '',
                $input->type,
                $input->provider ?? '',
                $input->externalAttachmentId ?? '',
                $input->referenceUri,
            );
        } catch (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $exception) {
            return new JsonResponse(['ok' => false, 'errors' => [$exception->getMessage()]], 403);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse(['ok' => false, 'errors' => [$exception->getMessage()]], 400);
        }

        return new JsonResponse([
            'ok' => true,
            'item' => $item,
        ], 201);
    }
    /**
     * Deletes the requested target from the underlying store.
     */
    #[Route('/api/category/attachment/{attachmentId}', name: 'api_category_attachment_delete', methods: ['DELETE'])]
    public function delete(string $attachmentId): JsonResponse
    {
        try {
            $this->authorizationService->assertCanDetach($attachmentId);
            $deleted = $this->service->remove($attachmentId);
        } catch (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $exception) {
            return new JsonResponse(['ok' => false, 'errors' => [$exception->getMessage()]], 403);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse(['ok' => false, 'errors' => [$exception->getMessage()]], 400);
        }

        if (!$deleted) {
            return new JsonResponse(['ok' => false, 'errors' => ['attachment was not found']], 404);
        }

        return new JsonResponse([
            'ok' => true,
            'attachment_id' => trim($attachmentId),
        ]);
    }
}
