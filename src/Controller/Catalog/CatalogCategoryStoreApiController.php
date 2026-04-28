<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller\Catalog;

use App\Cataloging\Service\CatalogChannelFilterService;
use App\Cataloging\Service\CatalogReadOptimizerService;
use App\Cataloging\ValueObject\CategoryProjectionCriteria;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Store API delivery adapter over shared read services.
 */
final readonly class CatalogCategoryStoreApiController
{
    /**
     * Initializes the category store api controller service collaborators.
     */
    public function __construct(
        private CatalogChannelFilterService $filter,
        private CatalogReadOptimizerService $optimizer,
    ) {
    }

    /**
     * Executes the invokable workflow for this service.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     * @throws \JsonException
     */
    #[Route('/api/category/store', name: 'api_category_store', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $channel = trim((string) $request->query->get('channel', 'default'));
        $tenant = trim((string) $request->query->get('tenant', ''));
        $locale = trim((string) $request->query->get('locale', ''));
        $criteria = CategoryProjectionCriteria::fromArray(array_filter([
            'published' => true,
            'tenant' => '' === $tenant ? null : $tenant,
            'locale' => '' === $locale ? null : $locale,
        ], static fn (mixed $value): bool => null !== $value));

        $tree = $this->optimizer->getTree($criteria);
        $normalizedChannel = '' === $channel ? 'default' : $channel;
        $filtered = $this->filter->filter($tree, $normalizedChannel);

        return new JsonResponse([
            'data' => $filtered,
            'channel' => $normalizedChannel,
            'criteria' => $criteria->toArray(),
        ]);
    }
}
