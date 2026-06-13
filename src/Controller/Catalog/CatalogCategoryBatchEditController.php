<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller\Catalog;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles the category batch edit controller application flow.
 */
final class CatalogCategoryBatchEditController extends AbstractController
{
    /**
     * Executes the invokable workflow for this service.
     */
    #[Route('/admin/category/batch-edit', name: 'admin_category_batch_edit', methods: ['GET', 'POST'])]
    public function __invoke(Request $request): array
    {
        $rows = [];
        if ($request->isMethod('POST')) {
            $rows = $request->request->all('rows');
        }

        return [
            '_view' => [
                'surface' => 'category',
                'operation' => 'admin-batch-edit',
                'intent' => 'admin',
                'component' => 'Cataloging',
                'format' => 'auto',
            ],
            'locations' => ['body' => ['rows' => $rows]],
            'data' => ['rows' => $rows],
            'meta' => ['source' => 'catalog_category_batch_edit_controller'],
        ];
    }
}
