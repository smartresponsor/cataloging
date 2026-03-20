<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Controller;

use App\Service\AttachmentService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
        $data = json_decode($request->getContent(), true) ?? [];
        $this->service->add((string) ($data['category_id'] ?? ''), (string) ($data['type'] ?? 'icon'), (string) ($data['path'] ?? ''));

        return new JsonResponse(['ok' => true]);
    }
}
