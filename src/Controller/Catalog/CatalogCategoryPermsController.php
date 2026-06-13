<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller\Catalog;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles the category perms controller application flow.
 */
final class CatalogCategoryPermsController extends AbstractController
{
    /**
     * Executes the invokable workflow for this service.
     */
    #[Route('/admin/category/perms', name: 'admin_category_perms')]
    public function __invoke(): array
    {
        $perms = [
            ['role' => 'ROLE_ADMIN', 'create' => true, 'move' => true, 'publish' => true],
            ['role' => 'ROLE_MERCHANT', 'create' => false, 'move' => false, 'publish' => false],
        ];

        return [
            '_view' => [
                'surface' => 'category',
                'operation' => 'admin-perms',
                'intent' => 'admin',
                'component' => 'Cataloging',
                'format' => 'auto',
            ],
            'locations' => ['body' => ['perms' => $perms]],
            'data' => ['perms' => $perms],
            'meta' => ['source' => 'catalog_category_perms_controller'],
        ];
    }
}
