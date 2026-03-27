<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Request\CategoryBulkRequest;
use App\Service\BulkOperator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryBulkController
{
    public function __construct(private readonly BulkOperator $bulk)
    {
    }

    #[Route('/admin/category/bulk', name: 'admin_category_bulk', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $input = CategoryBulkRequest::fromJson((string) $request->getContent());
        if (!$input->isValid()) {
            return new JsonResponse(['errors' => $input->getErrors()], 400);
        }

        return new JsonResponse($this->bulk->run($input->ids, $input->action));
    }
}
