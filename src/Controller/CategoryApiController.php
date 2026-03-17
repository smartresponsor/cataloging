<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Request\MoveCategoryRequest;
use App\Request\PublishCategoryRequest;
use App\Service\CategoryDeliveryPipeline;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryApiController
{
    public function __construct(
        private readonly ?CategoryRepository $repository = null,
        private readonly ?CategoryDeliveryPipeline $deliveryPipeline = null,
    ) {
    }

    #[Route('/api/category/tree', name: 'api_category_tree', methods: ['GET'])]
    public function tree(?Request $request = null): JsonResponse
    {
        $request ??= new Request();
        $repo = $this->repository ?? $this->seededRepository();

        $taxonomy = (string) $request->query->get('taxonomy', 'catalog');
        $locale = (string) $request->query->get('locale', 'en');
        $depth = max(1, min(5, (int) $request->query->get('depth', 3)));
        $publishedOnly = filter_var($request->query->get('publishedOnly', '1'), FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
        $rows = true === $publishedOnly
            ? $repo->publishedTree($taxonomy, null, $depth, $locale)
            : $repo->tree($taxonomy, null, $depth, $locale);

        return new JsonResponse([
            'ok' => true,
            'data' => array_values($rows),
            'count' => count($rows),
            'taxonomy' => $taxonomy,
            'locale' => $locale,
            'pageInfo' => ['depth' => $depth],
        ]);
    }

    #[Route('/api/category/{id}/move', name: 'api_category_move', methods: ['POST'])]
    public function move(string $id, Request $request): JsonResponse
    {
        $dto = MoveCategoryRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        if (!$dto->isValid()) {
            return new JsonResponse(['ok' => false, 'errors' => $dto->getErrors(), 'nodeId' => $id], 400);
        }

        $order = max(0, (int) ((json_decode($request->getContent(), true) ?? [])['order'] ?? 0));
        $result = ($this->repository ?? $this->seededRepository())->move('api', $id, $dto->parentId, $order);

        return new JsonResponse([
            'ok' => true,
            'action' => 'move',
            'nodeId' => $id,
            'parentId' => $result['parentId'] ?? $dto->parentId,
            'order' => $result['order'] ?? $order,
            'path' => $result['path'] ?? null,
        ]);
    }

    #[Route('/api/category/{id}/publish', name: 'api_category_publish', methods: ['POST'])]
    public function publish(string $id, Request $request): JsonResponse
    {
        $dto = PublishCategoryRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        if (!$dto->isValid()) {
            return new JsonResponse(['ok' => false, 'errors' => $dto->getErrors(), 'nodeId' => $id], 400);
        }

        $repository = $this->repository ?? $this->seededRepository();
        $updated = $repository->setPublished($id, (bool) $dto->published, 'api');
        if ([] === $updated) {
            return new JsonResponse(['ok' => false, 'errors' => ['category not found'], 'nodeId' => $id], 404);
        }

        $delivery = null;
        if (null !== $this->deliveryPipeline) {
            $delivery = $this->deliveryPipeline->deliver(
                true === $dto->published ? 'category.published' : 'category.unpublished',
                [
                    'id' => $id,
                    'taxonomyId' => (string) ($updated['taxonomyId'] ?? 'catalog'),
                    'path' => (string) ($updated['path'] ?? ''),
                    'published' => (bool) $dto->published,
                ],
                'https://example.test/webhook'
            );
        }

        return new JsonResponse([
            'ok' => true,
            'action' => 'publish',
            'nodeId' => $id,
            'published' => $dto->published,
            'state' => true === $dto->published ? 'published' : 'draft',
            'data' => $repository->findById($id, 'en'),
            'delivery' => $delivery,
        ]);
    }

    private function seededRepository(): CategoryRepository
    {
        $repository = new CategoryRepository();
        $repository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root', 'uk' => 'Корінь'], 'slug' => ['en' => 'root', 'uk' => 'koren'], 'meta' => ['published' => true]],
            ['id' => 'electronics', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Electronics', 'uk' => 'Електроніка'], 'slug' => ['en' => 'electronics', 'uk' => 'elektronika'], 'meta' => ['published' => true]],
            ['id' => 'hidden', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Hidden'], 'slug' => ['en' => 'hidden'], 'meta' => ['published' => false]],
        ]);

        return $repository;
    }
}
