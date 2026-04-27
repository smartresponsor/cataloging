<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller\Catalog;

use App\Cataloging\Dto\CategoryAdminCategoryData;
use App\Cataloging\Form\CategoryAdminCategoryType;
use App\Cataloging\ServiceInterface\CatalogCategoryProjectionReadServiceInterface;
use App\Cataloging\ValueObject\CategoryProjectionCriteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
/**
 * Admin UI delivery adapter over shared projection-backed catalog services.
 */
final class CatalogCategoryAdminController extends AbstractController
{
    /**
     * Initializes the category admin controller service collaborators.
     */
    public function __construct(private readonly CatalogCategoryProjectionReadServiceInterface $categoryProjectionReadService)
    {
    }

    /**
     * Handles the index workflow.
     */
    #[Route('/admin/category', name: 'admin_category_index')]
    public function index(): Response
    {
        try {
            $categories = $this->categoryProjectionReadService->list(CategoryProjectionCriteria::fromArray([
                'limit' => 100,
                'offset' => 0,
                'order' => 'name',
                'direction' => 'asc',
            ]));
        } catch (\Throwable $exception) {
            throw $this->createNotFoundException('Unable to load category administration listing.', $exception);
        }

        return $this->render('category/list.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * Handles the new workflow.
     */
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

    /**
     * Handles the edit workflow.
     */
    #[Route('/admin/category/{id}/edit', name: 'admin_category_edit')]
    public function edit(int $id, Request $request): Response
    {
        try {
            $category = $this->categoryProjectionReadService->findOne((string) $id);
        } catch (\Throwable $exception) {
            throw $this->createNotFoundException(sprintf('Unable to load category #%d.', $id), $exception);
        }
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

    /**
     * Handles the tree workflow.
     */
    #[Route('/admin/category/tree', name: 'admin_category_tree')]
    public function tree(): Response
    {
        try {
            $tree = $this->categoryProjectionReadService->tree();
        } catch (\Throwable $exception) {
            throw $this->createNotFoundException('Unable to load category tree.', $exception);
        }

        return $this->render('category/tree.html.twig', [
            'tree' => $tree,
        ]);
    }
}
