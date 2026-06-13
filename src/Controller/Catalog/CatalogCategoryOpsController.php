<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller\Catalog;

use App\Cataloging\Service\CategoryPayloadValueNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles the category ops controller application flow.
 */
final class CatalogCategoryOpsController extends AbstractController
{
    /**
     * Executes the invokable workflow for this service.
     */
    #[Route('/admin/category/ops', name: 'admin_category_ops')]
    public function __invoke(): array
    {
        $data = [
            'slo' => $this->readJsonFile('report/category-slo-ci.json'),
            'dlq' => $this->readJsonFile('report/category-dlq.json'),
            'canary' => $this->readJsonFile('report/category-canary-window.json'),
        ];

        return [
            '_view' => [
                'surface' => 'category',
                'operation' => 'admin-ops',
                'intent' => 'admin',
                'component' => 'Cataloging',
                'format' => 'auto',
            ],
            'locations' => ['body' => $data],
            'data' => $data,
            'meta' => ['source' => 'catalog_category_ops_controller'],
        ];
    }

    /** @return array<string,mixed> */
    private function readJsonFile(string $path): array
    {
        if (!is_file($path) || !is_readable($path)) {
            return [];
        }

        $content = file_get_contents($path);
        if (false === $content) {
            return [];
        }

        return CategoryPayloadValueNormalizer::nestedMap(json_decode($content, true));
    }
}
