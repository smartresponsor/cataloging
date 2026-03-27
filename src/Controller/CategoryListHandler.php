<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Service\Category\CategoryApproxTotalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryListHandler extends AbstractController
{
    #[Route('/category/list', name: 'category_list_handler', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $withTotal = $request->query->getBoolean('withTotal');
        $approx = new CategoryApproxTotalService(__DIR__.'/../../var/counters.json');
        $items = [
            ['id' => 'catA', 'name' => 'Root', 'slug' => 'root', 'catalogId' => null, 'rank' => 0],
            ['id' => 'catB', 'name' => 'Shoes', 'slug' => 'shoes', 'catalogId' => null, 'rank' => 10],
        ];

        return $this->json([
            'items' => $items,
            'total' => $approx->get('categoryList', $withTotal),
        ]);
    }
}
