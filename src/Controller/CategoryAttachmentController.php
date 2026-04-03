<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Request\CategoryAttachmentAddRequest;
use App\Service\AttachmentService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CategoryAttachmentController
{
    public function __construct(private readonly AttachmentService $service)
    {
    }

    #[Route('/api/category/attachment', name: 'api_category_attachment_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $categoryId = $request->query->get('category_id');
        $normalizedCategoryId = is_string($categoryId) ? trim($categoryId) : '';

        return new JsonResponse([
            'ok' => true,
            'items' => $this->service->list('' !== $normalizedCategoryId ? $normalizedCategoryId : null),
        ]);
    }

    #[Route('/api/category/attachment', name: 'api_category_attachment_add', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request): JsonResponse
    {
        $input = CategoryAttachmentAddRequest::fromJson((string) $request->getContent());
        if (!$input->isValid()) {
            return new JsonResponse(['ok' => false, 'errors' => $input->getErrors()], 400);
        }

        try {
            $item = $this->service->add($input->categoryId ?? '', $input->type, $input->path ?? '');
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse(['ok' => false, 'errors' => [$exception->getMessage()]], 400);
        }

        return new JsonResponse([
            'ok' => true,
            'item' => $item,
        ], 201);
    }

    #[Route('/api/category/attachment/{attachmentId}', name: 'api_category_attachment_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(string $attachmentId): JsonResponse
    {
        try {
            $deleted = $this->service->remove($attachmentId);
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
