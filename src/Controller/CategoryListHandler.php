<?php

declare(strict_types=1);

/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 */

namespace App\Controller;

use App\Service\Category\ApproxTotalService;
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
        $approx = new ApproxTotalService(__DIR__.'/../../var/counters.json');
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
