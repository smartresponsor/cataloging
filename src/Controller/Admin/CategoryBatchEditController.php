<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryBatchEditController extends AbstractController
{
    #[Route('/admin/category/batch-edit', name: 'admin_category_batch_edit', methods: ['GET', 'POST'])]
    public function __invoke(Request $request): Response
    {
        $rows = [];
        if ($request->isMethod('POST')) {
            $rows = $request->request->all('rows');
        }

        return $this->render('category/admin/batch_edit.html.twig', [
            'rows' => $rows,
        ]);
    }
}
