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

final class CategoryStorefrontController
{
    public function __construct(
        private readonly ReadOptimizer $optimizer,
        private readonly ChannelFilter $filter,
    ) {
    }

    #[Route('/api/category/storefront', name: 'api_category_storefront', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $channel = (string) $request->query->get('channel', 'default');
        $locale = (string) $request->query->get('locale', '');

        $tree = $this->optimizer->getTree();
        $filtered = $this->filter->filter($tree, $channel);
        $filtered = array_values(array_filter($filtered, static function (array $row) use ($locale): bool {
            if (($row['published'] ?? false) !== true) {
                return false;
            }

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
