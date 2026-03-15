<?php

declare(strict_types=1);

/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 */

namespace SmartResponsor\Category\Http;

use App\Service\Category\Domain\ApproxTotalService;
use App\Service\Category\Domain\CollectionService;

final class CollectionListHandler
{
    public function handle(): void
    {
        $rule = $_GET['rule'] ?? 'tag:winter AND price<100';
        $withTotal = isset($_GET['withTotal']) && 'true' === $_GET['withTotal'];
        $svc = new CollectionService();
        $products = [
            ['id' => 'p1', 'price' => 79, 'tags' => ['winter', 'sale'], 'categoryIds' => ['catA']],
            ['id' => 'p2', 'price' => 129, 'tags' => ['summer'], 'categoryIds' => ['catB']],
            ['id' => 'p3', 'price' => 59, 'tags' => ['winter'], 'categoryIds' => ['catA', 'catC']],
        ];
        $filtered = $svc->filter($products, $rule);
        $approx = new ApproxTotalService(__DIR__.'/../../var/counters.json');
        header('Content-Type: application/json');
        echo json_encode(['items' => $filtered, 'total' => $approx->get('collectionList', $withTotal)]);
    }
}
