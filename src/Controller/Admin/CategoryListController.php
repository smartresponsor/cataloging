<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller\Admin;

use App\Cataloging\ServiceInterface\CategoryProjectionReadServiceInterface;
use App\Cataloging\ValueObject\CategoryProjectionCriteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles the category list controller application flow.
 */
final class CategoryListController extends AbstractController
{
    /**
     * Initializes the category list controller service collaborators.
     */
    public function __construct(private readonly CategoryProjectionReadServiceInterface $categoryProjectionReadService)
    {
    }

    /**
     * Executes the invokable workflow for this service.
     */
    #[Route('/admin/category/list', name: 'admin_category_list', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $published = $request->query->getBoolean('published');

        try {
            $items = $this->categoryProjectionReadService->list(CategoryProjectionCriteria::fromArray([
                'published' => $published ? true : null,
                'limit' => 100,
                'offset' => 0,
                'order' => 'name',
                'direction' => 'asc',
            ]));
        } catch (\Throwable $exception) {
            throw $this->createNotFoundException('Unable to load category admin list.', $exception);
        }

        return $this->render('category/admin/list.html.twig', [
            'items' => $items,
            'page' => $page,
            'published' => $published,
        ]);
    }
}
