<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Request\CategoryBulkRequest;
use App\Service\BulkOperator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles the category bulk controller application flow.
 */
final readonly class CategoryBulkController
{
    /**
     * Initializes the category bulk controller service collaborators.
     */
    public function __construct(private BulkOperator $bulk)
    {
    }

    /**
     * Executes the invokable workflow for this service.
     */
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
