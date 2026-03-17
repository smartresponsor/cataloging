<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service;

use App\Repository\CategoryRepository;

final class CategoryMutationCoordinator
{
    public function __construct(
        private readonly CategoryRepository $repository,
        private readonly ?CategoryDeliveryPipeline $deliveryPipeline = null,
    ) {
    }

    public function publishMany(array $ids, bool $published, string $actorId = 'admin-api'): array
    {
        $result = $this->repository->bulkSetPublished($ids, $published, $actorId);
        $deliveries = [];

        if (null !== $this->deliveryPipeline) {
            foreach ($result['success'] as $row) {
                $entity = $this->repository->findById((string) $row['id'], 'en');
                $deliveries[] = $this->deliveryPipeline->deliver(
                    $published ? 'category.published' : 'category.unpublished',
                    [
                        'id' => (string) $row['id'],
                        'taxonomyId' => (string) ($entity['taxonomyId'] ?? 'catalog'),
                        'path' => (string) ($entity['path'] ?? ''),
                        'published' => $published,
                    ],
                    'https://example.test/webhook'
                );
            }
        }

        return [
            'success' => $result['success'],
            'failed' => $result['failed'],
            'deliveries' => $deliveries,
        ];
    }
}
