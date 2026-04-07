<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use App\Service\ChannelFilter;
use App\Service\ReadOptimizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Store API delivery adapter over shared read services.
 */
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
        $channel = trim((string) $request->query->get('channel', 'default'));
        $tenant = trim((string) $request->query->get('tenant', ''));
        $locale = trim((string) $request->query->get('locale', ''));
        $criteria = array_filter([
            'published' => true,
            'tenant' => '' === $tenant ? null : $tenant,
            'locale' => '' === $locale ? null : $locale,
        ], static fn (mixed $value): bool => null !== $value);

        $tree = $this->optimizer->getTree($criteria);
        $filtered = $this->filter->filter($tree, '' === $channel ? 'default' : $channel);

        return new JsonResponse([
            'data' => $filtered,
            'channel' => '' === $channel ? 'default' : $channel,
            'criteria' => $criteria,
        ]);
    }
}
