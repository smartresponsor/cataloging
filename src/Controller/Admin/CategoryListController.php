<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller\Admin;

use App\ServiceInterface\CategoryProjectionReadServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryListController extends AbstractController
{
    public function __construct(private readonly CategoryProjectionReadServiceInterface $categoryProjectionReadService)
    {
    }

    #[Route('/admin/category/list', name: 'admin_category_list', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $published = $request->query->getBoolean('published', false);
        $items = $this->categoryProjectionReadService->list([
            'published' => $published ? true : null,
            'limit' => 100,
            'offset' => 0,
            'order' => 'name',
            'direction' => 'asc',
        ]);

        return $this->render('category/admin/list.html.twig', [
            'items' => $items,
            'page' => $page,
            'published' => $published,
        ]);
    }
}
