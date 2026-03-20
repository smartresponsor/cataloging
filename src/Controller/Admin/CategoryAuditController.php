<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryAuditController extends AbstractController
{
    #[Route('/admin/category/audit', name: 'admin_category_audit')]
    public function __invoke(): Response
    {
        $file = 'report/category-telemetry.ndjson';
        $rows = [];
        if (is_file($file)) {
            foreach (file($file, FILE_IGNORE_NEW_LINES) as $line) {
                $rows[] = json_decode($line, true, flags: JSON_THROW_ON_ERROR);
            }
        }

        return $this->render('category/admin/audit.html.twig', [
            'rows' => $rows,
        ]);
    }
}
