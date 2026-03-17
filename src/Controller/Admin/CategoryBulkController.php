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

final class CategoryBulkController
{
    private const ALLOWED_ACTIONS = ['publish', 'unpublish', 'archive', 'reindex'];

    public function __construct(private readonly BulkOperator $bulk)
    {
    }

    #[Route('/admin/category/bulk', name: 'admin_category_bulk', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true) ?? [];
        $ids = $payload['ids'] ?? [];
        $action = (string) ($payload['action'] ?? 'publish');

        if (!is_array($ids)) {
            return new JsonResponse(['ok' => false, 'error' => ['ids must be an array']], 400);
        }
        if (!in_array($action, self::ALLOWED_ACTIONS, true)) {
            return new JsonResponse(['ok' => false, 'error' => ['action is invalid']], 400);
        }

        $res = $this->bulk->run($ids, $action);

        return new JsonResponse([
            'ok' => true,
            'action' => $action,
            'successCount' => count($res['success']),
            'failedCount' => count($res['failed']),
            'result' => $res,
        ]);
    }
}
