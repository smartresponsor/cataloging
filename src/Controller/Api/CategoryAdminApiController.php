<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Controller\Api;

use App\Repository\CategoryRepository;
use App\Service\BulkOperator;
use App\Service\CategoryMutationCoordinator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryAdminApiController
{
    private const ALLOWED_ACTIONS = ['publish', 'unpublish', 'archive', 'reindex'];

    public function __construct(
        private readonly ?BulkOperator $bulk = null,
        private readonly ?CategoryRepository $repository = null,
        private readonly ?CategoryMutationCoordinator $mutationCoordinator = null,
    ) {
    }

    #[Route('/api/admin/category/list', name: 'api_admin_category_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $taxonomy = (string) $request->query->get('taxonomy', 'catalog');
        $locale = (string) $request->query->get('locale', 'en');
        $depth = max(1, min(5, (int) $request->query->get('depth', 3)));

        $repository = $this->repository ?? $this->seededRepository();
        $rows = $repository->tree($taxonomy, null, $depth, $locale);

        return new JsonResponse([
            'ok' => true,
            'taxonomy' => $taxonomy,
            'locale' => $locale,
            'count' => count($rows),
            'items' => array_values($rows),
            'pageInfo' => ['depth' => $depth, 'includeDrafts' => true],
        ]);
    }

    #[Route('/api/admin/category/bulk', name: 'api_admin_category_bulk', methods: ['POST'])]
    public function bulk(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true) ?? [];
        $ids = $payload['ids'] ?? [];
        $action = (string) ($payload['action'] ?? 'publish');

        if (!is_array($ids)) {
            return new JsonResponse(['ok' => false, 'error' => ['ids must be an array']], 400);
        }
        if (!in_array($action, self::ALLOWED_ACTIONS, true)) {
            return new JsonResponse(['ok' => false, 'error' => ['action is invalid']], 400);
        }

        if (null !== $this->mutationCoordinator && in_array($action, ['publish', 'unpublish'], true)) {
            $result = $this->mutationCoordinator->publishMany($ids, 'publish' === $action, 'admin-api');
        } else {
            $result = ($this->bulk ?? new BulkOperator($this->repository))->run($ids, $action);
            $result['deliveries'] = [];
        }

        $publicRows = null !== $this->repository && in_array($action, ['publish', 'unpublish'], true)
            ? $this->repository->publishedTree('catalog', null, 5, 'en')
            : [];

        return new JsonResponse([
            'ok' => true,
            'action' => $action,
            'successCount' => count($result['success']),
            'failedCount' => count($result['failed']),
            'deliveryCount' => count($result['deliveries']),
            'publicCountAfter' => count($publicRows),
            'publicIdsAfter' => array_values(array_map(static fn (array $row): string => (string) $row['id'], $publicRows)),
            'result' => $result,
        ]);
    }

    private function seededRepository(): CategoryRepository
    {
        $repository = new CategoryRepository();
        $repository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root', 'uk' => 'Корінь'], 'slug' => ['en' => 'root', 'uk' => 'koren'], 'meta' => ['published' => true]],
            ['id' => 'electronics', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Electronics', 'uk' => 'Електроніка'], 'slug' => ['en' => 'electronics', 'uk' => 'elektronika'], 'meta' => ['published' => true]],
            ['id' => 'hidden', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Hidden', 'uk' => 'Прихована'], 'slug' => ['en' => 'hidden', 'uk' => 'pryhovana'], 'meta' => ['published' => false]],
        ]);

        return $repository;
    }
}
