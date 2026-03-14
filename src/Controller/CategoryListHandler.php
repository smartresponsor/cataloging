<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Controller;

use App\Service\Query\Category\ApproxTotalService;

final class CategoryListHandler
{
    public function handle(): void
    {
        $withTotal = isset($_GET['withTotal']) && 'true' === $_GET['withTotal'];
        $approxTotal = new ApproxTotalService(__DIR__.'/../../../var/counters.json');
        $itemList = [
            ['id' => 'catA', 'name' => 'Root', 'slug' => 'root', 'catalogId' => null, 'rank' => 0],
            ['id' => 'catB', 'name' => 'Shoes', 'slug' => 'shoes', 'catalogId' => null, 'rank' => 10],
        ];

        header('Content-Type: application/json');
        echo json_encode(['items' => $itemList, 'total' => $approxTotal->get('categoryList', $withTotal)]);
    }
}
