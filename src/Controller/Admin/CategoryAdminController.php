<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Dto\CategoryAdminCategoryData;
use App\Form\CategoryAdminCategoryType;
use App\ServiceInterface\CategoryProjectionReadServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
/**
 * Admin UI delivery adapter over shared projection-backed catalog services.
 */
final class CategoryAdminController extends AbstractController
{
    public function __construct(private readonly CategoryProjectionReadServiceInterface $categoryProjectionReadService)
    {
    }

    #[Route('/admin/category', name: 'admin_category_index')]
    public function index(): Response
    {
        $categories = $this->categoryProjectionReadService->list([
            'limit' => 100,
            'offset' => 0,
            'order' => 'name',
            'direction' => 'asc',
        ]);

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
            $this->addFlash('success', sprintf('Category "%s" captured for baseline save.', $data->name));

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
        $category = $this->categoryProjectionReadService->findOne((string) $id);
        if (null === $category) {
            throw $this->createNotFoundException(sprintf('Category #%d was not found.', $id));
        }

        $data = CategoryAdminCategoryData::fromArray($category);
        $form = $this->createForm(CategoryAdminCategoryType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', sprintf('Category #%d updated in baseline flow.', $id));

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
        $tree = $this->categoryProjectionReadService->tree();

        return $this->render('category/tree.html.twig', [
            'tree' => $tree,
        ]);
    }
}
