<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
/**
 * Handles the category audit controller application flow.
 */
final class CategoryAuditController extends AbstractController
{
    /**
     * Executes the invokable workflow for this service.
     */
    #[Route('/admin/category/audit', name: 'admin_category_audit')]
    public function __invoke(): Response
    {
        $file = 'report/category-telemetry.ndjson';
        $rows = [];
        if (is_file($file)) {
            $lines = file($file, FILE_IGNORE_NEW_LINES);
            foreach (is_array($lines) ? $lines : [] as $line) {
                if (!is_string($line) || '' === trim($line)) {
                    continue;
                }
                $decoded = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
                if (is_array($decoded)) {
                    $rows[] = $decoded;
                }
            }
        }

        return $this->render('category/admin/audit.html.twig', [
            'rows' => $rows,
        ]);
    }
}
