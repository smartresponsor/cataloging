<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Controller\Admin;

use App\Service\BulkOperator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class testsBulkController
{
    public function __construct(private readonly BulkOperator $bulk)
    {
    }

    #[Route('/admin/category/bulk', name: 'admin_category_bulk', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true) ?? [];
        $ids = $payload['ids'] ?? [];
        $action = $payload['action'] ?? 'publish';
        $res = $this->bulk->run($ids, $action);

        return new JsonResponse($res);
    }
}
