<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryOpsController extends AbstractController
{
    #[Route('/admin/category/ops', name: 'admin_category_ops')]
    public function __invoke(): Response
    {
        $slo = @file_get_contents('report/catalog-slo-ci.json') ?: '{}';
        $dlq = @file_get_contents('report/catalog-dlq.json') ?: '[]';
        $canary = @file_get_contents('report/catalog-canary-window.json') ?: '{}';

        return $this->render('category/admin/ops.html.twig', [
            'slo' => json_decode($slo, true),
            'dlq' => json_decode($dlq, true),
            'canary' => json_decode($canary, true),
        ]);
    }
}
