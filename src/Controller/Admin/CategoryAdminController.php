<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Dto\CategoryAdminCategoryData;
use App\Form\CategoryAdminCategoryType;
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
        $data = new CategoryAdminCategoryData();
        $form = $this->createForm(CategoryAdminCategoryType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', sprintf('Category "%s" prepared for demo save.', $data->name));

            return $this->redirectToRoute('admin_category_index');
        }

        return $this->render('category/form.html.twig', [
            'form' => $form->createView(),
            'is_edit' => false,
        ]);
    }

    #[Route('/admin/category/{id}/edit', name: 'admin_category_edit')]
    public function edit(int $id, Request $request): Response
    {
        $category = ['id' => $id, 'name' => 'Electronics', 'slug' => 'electronics'];
        $data = CategoryAdminCategoryData::fromArray($category);
        $form = $this->createForm(CategoryAdminCategoryType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', sprintf('Category #%d updated in demo flow.', $id));

            return $this->redirectToRoute('admin_category_index');
        }

        return $this->render('category/form.html.twig', [
            'form' => $form->createView(),
            'is_edit' => true,
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
