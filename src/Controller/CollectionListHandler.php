<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Controller;

use App\Service\Query\Category\ApproxTotalService;
use App\Service\Query\Category\CollectionService;

final class CollectionListHandler
{
    public function handle(): void
    {
        $rule = (string) ($_GET['rule'] ?? 'tag:winter AND price<100');
        $withTotal = isset($_GET['withTotal']) && 'true' === $_GET['withTotal'];
        $service = new CollectionService();
        $productList = [
            ['id' => 'p1', 'price' => 79, 'tags' => ['winter', 'sale'], 'categoryIds' => ['catA']],
            ['id' => 'p2', 'price' => 129, 'tags' => ['summer'], 'categoryIds' => ['catB']],
            ['id' => 'p3', 'price' => 59, 'tags' => ['winter'], 'categoryIds' => ['catA', 'catC']],
        ];
        $filtered = $service->filter($productList, $rule);
        $approxTotal = new ApproxTotalService(__DIR__.'/../../../var/counters.json');

        header('Content-Type: application/json');
        echo json_encode(['items' => $filtered, 'total' => $approxTotal->get('collectionList', $withTotal)]);
    }
}
