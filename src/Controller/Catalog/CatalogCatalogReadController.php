<?php

declare(strict_types=1);

namespace App\Cataloging\Controller\Catalog;

use App\Cataloging\ServiceInterface\CatalogCatalogTreeReadServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final readonly class CatalogCatalogReadController
{
    public function __construct(private CatalogCatalogTreeReadServiceInterface $catalogTreeReadService)
    {
    }

    #[Route(
        '/api/catalog/{catalogCode}/category/tree',
        name: 'api_catalog_category_tree_by_code',
        requirements: ['catalogCode' => '[a-z0-9][a-z0-9-]{1,63}'],
        methods: ['GET'],
    )]
    public function tree(string $catalogCode): JsonResponse
    {
        $tree = $this->catalogTreeReadService->byCode($catalogCode);
        if (null === $tree) {
            return new JsonResponse(['ok' => false, 'error' => 'catalog_not_found'], 404);
        }

        return new JsonResponse(['ok' => true, ...$tree]);
    }
}
