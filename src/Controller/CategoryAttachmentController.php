<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Request\CategoryAttachmentAddRequest;
use App\Service\AttachmentService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryAttachmentController
{
    public function __construct(private readonly AttachmentService $service)
    {
    }

    #[Route('/api/category/attachment', name: 'api_category_attachment_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return new JsonResponse($this->service->list());
    }

    #[Route('/api/category/attachment', name: 'api_category_attachment_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $input = CategoryAttachmentAddRequest::fromJson((string) $request->getContent());
        if (!$input->isValid()) {
            return new JsonResponse(['errors' => $input->getErrors()], 400);
        }

        $this->service->add($input->categoryId ?? '', $input->type, $input->path ?? '');

        return new JsonResponse(['ok' => true]);
    }
}
