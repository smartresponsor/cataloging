<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Controller;

use App\Service\ChannelFilter;
use App\Service\ReadOptimizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryStoreApiController
{
    public function __construct(
        private readonly ChannelFilter $filter,
        private readonly ReadOptimizer $optimizer,
    ) {
    }

    #[Route('/api/category/store', name: 'api_category_store', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $channel = (string) $request->query->get('channel', 'default');
        $locale = (string) $request->query->get('locale', '');
        $tree = $this->optimizer->getTree();
        $filtered = $this->filter->filter($tree, $channel);
        $filtered = array_values(array_filter($filtered, static function (array $row) use ($locale): bool {
            return '' === $locale || ($row['locale'] ?? '') === $locale;
        }));

        return new JsonResponse([
            'data' => $filtered,
            'channel' => $channel,
            'locale' => $locale,
            'count' => count($filtered),
        ]);
    }
}
