<?php

declare(strict_types=1);

/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 */

namespace SmartResponsor\Http;

use App\Service\CatalogCategory\ApproxTotalService;

final class CategoryListHandler
{
    public function handle(): void
    {
        $withTotal = isset($_GET['withTotal']) && 'true' === $_GET['withTotal'];
        $approx = new ApproxTotalService(__DIR__.'/../../var/counters.json');
        $items = [
            ['id' => 'catA', 'name' => 'Root', 'slug' => 'root', 'catalogId' => null, 'rank' => 0],
            ['id' => 'catB', 'name' => 'Shoes', 'slug' => 'shoes', 'catalogId' => null, 'rank' => 10],
        ];
        header('Content-Type: application/json');
        echo json_encode(['items' => $items, 'total' => $approx->get('categoryList', $withTotal)]);
    }
}
