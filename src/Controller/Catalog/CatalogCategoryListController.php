<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller\Catalog;

use App\Cataloging\ServiceInterface\CatalogCategoryProjectionReadServiceInterface;
use App\Cataloging\ValueObject\CategoryProjectionCriteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles the category list controller application flow.
 */
final class CatalogCategoryListController extends AbstractController
{
    /**
     * Initializes the category list controller service collaborators.
     */
    public function __construct(private readonly CatalogCategoryProjectionReadServiceInterface $categoryProjectionReadService)
    {
    }

    /**
     * Executes the invokable workflow for this service.
     */
    #[Route('/admin/category/list', name: 'admin_category_list', methods: ['GET'])]
    public function __invoke(Request $request): array
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $published = $request->query->getBoolean('published');

        try {
            $items = $this->categoryProjectionReadService->list(CategoryProjectionCriteria::fromArray([
                'published' => $published ? true : null,
                'limit' => 100,
                'offset' => 0,
                'order' => 'nameEntity',
                'direction' => 'asc',
            ]));
        } catch (\Throwable $exception) {
            throw $this->createNotFoundException('Unable to load category admin list.', $exception);
        }

        return [
            '_view' => [
                'surface' => 'category',
                'operation' => 'admin-list',
                'intent' => 'admin',
                'component' => 'Cataloging',
                'format' => 'auto',
            ],
            'locations' => ['body' => ['items' => $items, 'page' => $page, 'published' => $published]],
            'data' => ['items' => $items, 'page' => $page, 'published' => $published],
            'meta' => ['source' => 'catalog_category_list_controller'],
        ];
    }
}
