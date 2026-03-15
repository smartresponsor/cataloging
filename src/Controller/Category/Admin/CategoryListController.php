<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Controller\Category\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryListController extends AbstractController
{
    #[Route('/admin/category/list', name: 'admin_category_list', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $published = $request->query->getBoolean('published', false);
        $items = [
            ['id' => 1, 'name' => 'Root', 'published' => true],
            ['id' => 2, 'name' => 'Electronics', 'published' => true],
            ['id' => 3, 'name' => 'Draft', 'published' => false],
        ];
        if ($published) {
            $items = array_values(array_filter($items, static fn ($x) => $x['published']));
        }

        return $this->render('category/admin/list.html.twig', [
            'items' => $items,
            'page' => $page,
            'published' => $published,
        ]);
    }
}
