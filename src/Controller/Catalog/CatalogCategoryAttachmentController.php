<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller\Catalog;

use App\Cataloging\Request\CategoryAttachmentAddRequest;
use App\Cataloging\Service\CatalogAttachmentService;
use App\Cataloging\Service\CatalogCategoryAttachmentAuthorizationService;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles the category attachment controller application flow.
 */
final readonly class CatalogCategoryAttachmentController
{
    /**
     * Initializes the category attachment controller service collaborators.
     */
    public function __construct(
        private CatalogAttachmentService $attachmentService,
        private CatalogCategoryAttachmentAuthorizationService $authorizationService,
    ) {
    }

    /**
     * Handles the list workflow.
     */
    #[Route('/api/catalog/category/attachment', name: 'api_category_attachment_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $categoryId = $request->query->get('category_id');
        $categoryId = is_scalar($categoryId) ? trim((string) $categoryId) : '';
        $categoryId = '' !== $categoryId ? $categoryId : null;

        try {
            $this->authorizationService->assertCanList($categoryId);

            return new JsonResponse([
                'ok' => true,
                'items' => $this->attachmentService->list($categoryId),
            ]);
        } catch (AccessDeniedHttpException $exception) {
            return new JsonResponse(['ok' => false, 'errors' => [$exception->getMessage()]], 403);
        } catch (Exception) {
            return new JsonResponse(['ok' => false, 'errors' => ['Unable to list attachments.']], 500);
        }
    }

    /**
     * Handles the attach workflow.
     */
    #[Route('/api/catalog/category/attachment', name: 'api_category_attachment_add', methods: ['POST'])]
    public function attach(Request $request): JsonResponse
    {
        $input = CategoryAttachmentAddRequest::fromJson((string) $request->getContent());
        if (!$input->isValid()) {
            return new JsonResponse(['ok' => false, 'errors' => $input->getErrors()], 400);
        }

        try {
            $categoryId = $input->categoryId;
            $provider = $input->provider;
            $externalAttachmentId = $input->externalAttachmentId;
            if (null === $categoryId || null === $provider || null === $externalAttachmentId) {
                return new JsonResponse(['ok' => false, 'errors' => ['Invalid attachment payload.']], 400);
            }

            $this->authorizationService->assertCanAttach($categoryId);

            $item = $this->attachmentService->add(
                $categoryId,
                $input->type,
                $provider,
                $externalAttachmentId,
                $input->referenceUri,
            );

            $attachmentId = $item['attachment_id'] ?? null;

            return new JsonResponse([
                'ok' => true,
                'attachment_id' => is_scalar($attachmentId) ? trim((string) $attachmentId) : '',
            ], 201);
        } catch (AccessDeniedHttpException $exception) {
            return new JsonResponse(['ok' => false, 'errors' => [$exception->getMessage()]], 403);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse(['ok' => false, 'errors' => [$exception->getMessage()]], 400);
        } catch (Exception) {
            return new JsonResponse(['ok' => false, 'errors' => ['Unable to attach media.']], 500);
        }
    }

    /**
     * Handles the detach workflow.
     */
    #[Route('/api/catalog/category/attachment/{attachmentId}', name: 'api_category_attachment_detach', methods: ['DELETE'])]
    public function detach(string $attachmentId): JsonResponse
    {
        try {
            $this->authorizationService->assertCanDetach($attachmentId);
            $deleted = $this->attachmentService->remove($attachmentId);
        } catch (AccessDeniedHttpException $exception) {
            return new JsonResponse(['ok' => false, 'errors' => [$exception->getMessage()]], 403);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse(['ok' => false, 'errors' => [$exception->getMessage()]], 400);
        } catch (Exception) {
            return new JsonResponse(['ok' => false, 'errors' => ['Unable to detach media.']], 500);
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
