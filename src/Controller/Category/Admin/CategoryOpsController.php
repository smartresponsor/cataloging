<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Controller\Category\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryOpsController extends AbstractController
{
    #[Route('/admin/category/ops', name: 'admin_category_ops')]
    public function __invoke(): Response
    {
        $slo = @file_get_contents('report/category-slo-ci.json') ?: '{}';
        $dlq = @file_get_contents('report/category-dlq.json') ?: '[]';
        $canary = @file_get_contents('report/category-canary-window.json') ?: '{}';

        return $this->render('category/admin/ops.html.twig', [
            'slo' => json_decode($slo, true),
            'dlq' => json_decode($dlq, true),
            'canary' => json_decode($canary, true),
        ]);
    }
}
