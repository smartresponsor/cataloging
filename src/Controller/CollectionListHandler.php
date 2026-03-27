<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Service\Category\CategoryApproxTotalService;
use App\Service\Category\CategoryCollectionService;
use App\ServiceInterface\Category\CategoryCollectionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CollectionListHandler extends AbstractController
{
    public function __construct(
        private readonly ?CategoryCollectionServiceInterface $service = null,
    ) {
    }

    #[Route('/collection/list', name: 'collection_list_handler', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $rule = (string) $request->query->get('rule', 'tag:winter AND price<100');
        $withTotal = $request->query->getBoolean('withTotal');
        $service = $this->service ?? new CategoryCollectionService();
        $products = [
            ['id' => 'p1', 'price' => 79, 'tags' => ['winter', 'sale'], 'categoryIds' => ['catA']],
            ['id' => 'p2', 'price' => 129, 'tags' => ['summer'], 'categoryIds' => ['catB']],
            ['id' => 'p3', 'price' => 59, 'tags' => ['winter'], 'categoryIds' => ['catA', 'catC']],
        ];
        $filtered = $service->filter($products, $rule);
        $approx = new CategoryApproxTotalService(__DIR__.'/../../var/counters.json');

        return $this->json([
            'items' => $filtered,
            'total' => $approx->get('collectionList', $withTotal),
        ]);
    }
}
