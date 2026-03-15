<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryAdminController extends AbstractController
{
    #[Route('/admin/category', name: 'admin_category_index')]
    public function index(): Response
    {
        $categories = [
            ['id' => 1, 'name' => 'Root', 'slug' => 'root'],
            ['id' => 2, 'name' => 'Electronics', 'slug' => 'electronics'],
        ];

        return $this->render('category/list.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/admin/category/new', name: 'admin_category_new')]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            return $this->redirectToRoute('admin_category_index');
        }

        return $this->render('category/form.html.twig', [
            'category' => null,
        ]);
    }

    #[Route('/admin/category/{id}/edit', name: 'admin_category_edit')]
    public function edit(int $id, Request $request): Response
    {
        $category = ['id' => $id, 'name' => 'Electronics', 'slug' => 'electronics'];
        if ($request->isMethod('POST')) {
            return $this->redirectToRoute('admin_category_index');
        }

        return $this->render('category/form.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/admin/category/tree', name: 'admin_category_tree')]
    public function tree(): Response
    {
        $tree = [
            ['id' => 1, 'name' => 'Root', 'children' => [
                ['id' => 2, 'name' => 'Electronics', 'children' => []],
            ]],
        ];

        return $this->render('category/tree.html.twig', [
            'tree' => $tree,
        ]);
    }
}
