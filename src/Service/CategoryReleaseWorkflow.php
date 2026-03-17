<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service;

use App\InfrastructureInterface\OutboxDispatcherInterface;
use App\Projection\CategoryProjectionRunner;
use App\RepositoryInterface\CategoryRepositoryInterface;

final class CategoryReleaseWorkflow
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository,
        private readonly TreeOperation $treeOperation,
        private readonly PublishOperation $publishOperation,
        private readonly OutboxDispatcherInterface $outboxDispatcher,
        private readonly CategoryProjectionRunner $projectionRunner,
    ) {
    }

    /**
     * @return array{category: array<string,mixed>, status: string}
     */
    public function createMovePublish(
        string $actorId,
        string $taxonomyId,
        ?string $parentId,
        array $name,
        array $slug,
        array $meta,
        ?string $newParentId,
        int $newOrder,
    ): array {
        $created = $this->repository->create($taxonomyId, $parentId, $name, $slug, $meta + ['published' => false]);

        $this->treeOperation->move((string) $created['id'], $newParentId);
        $moved = $this->repository->move($actorId, (string) $created['id'], $newParentId, $newOrder);

        $publishedStatus = $this->publishOperation->publish(new Status(Status::DRAFT));
        $published = $this->repository->setPublished((string) $created['id'], true, $actorId);

        $this->outboxDispatcher->dispatch([
            'id' => (string) $created['id'],
            'type' => 'category.moved',
            'payload' => [
                'categoryId' => (string) $created['id'],
                'parentId' => $newParentId,
                'order' => $newOrder,
            ],
        ]);

        $this->outboxDispatcher->dispatch([
            'id' => (string) $created['id'],
            'type' => 'category.published',
            'payload' => [
                'categoryId' => (string) $created['id'],
                'published' => true,
            ],
        ]);

        $this->projectionRunner->runOnce();

        return [
            'category' => array_replace($moved, [
                'taxonomyId' => $taxonomyId,
                'name' => $name,
                'slug' => $slug,
                'meta' => array_replace($meta, ['published' => true]),
            ], [] !== $published ? ['meta' => $published['meta'] ?? ['published' => true], 'state' => $published['state'] ?? 'published', 'updatedAt' => $published['updatedAt'] ?? null, 'updatedBy' => $published['updatedBy'] ?? $actorId] : []),
            'status' => $publishedStatus->value(),
        ];
    }
}
