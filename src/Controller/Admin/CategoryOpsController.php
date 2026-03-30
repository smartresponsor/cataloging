<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryOpsController extends AbstractController
{
    #[Route('/admin/category/ops', name: 'admin_category_ops')]
    public function __invoke(): Response
    {
        return $this->render('category/admin/ops.html.twig', [
            'slo' => $this->readJsonFile('report/category-slo-ci.json', []),
            'dlq' => $this->readJsonFile('report/category-dlq.json', []),
            'canary' => $this->readJsonFile('report/category-canary-window.json', []),
        ]);
    }

    /**
     * @param array<mixed> $default
     *
     * @return array<mixed>
     */
    private function readJsonFile(string $path, array $default): array
    {
        if (!is_file($path) || !is_readable($path)) {
            return $default;
        }

        $content = file_get_contents($path);
        if (false === $content) {
            return $default;
        }

        $decoded = json_decode($content, true);

        return is_array($decoded) ? $decoded : $default;
    }
}
