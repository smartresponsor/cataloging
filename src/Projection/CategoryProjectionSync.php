<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Projection;

use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use App\Cataloging\ProjectionInterface\CategoryProjectionSyncInterface;
use App\Cataloging\Service\CatalogCategoryProjectionSynchronizerService;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Applies supported category outbox events to the durable Doctrine read model.
 * Unknown events fail closed so the worker can retry or dead-letter them.
 */
final readonly class CategoryProjectionSync implements CategoryProjectionSyncInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CatalogCategoryProjectionSynchronizerService $synchronizer,
    ) {
    }

    /** @param array<string,mixed> $event */
    public function apply(array $event): void
    {
        $type = $event['type'] ?? null;
        if (!is_string($type) || !in_array($type, [
            'category.moved',
            'category.published',
            'category.unpublished',
        ], true)) {
            throw new \InvalidArgumentException('Unsupported category projection event type.');
        }

        $payload = $event['payload'] ?? null;
        $categoryId = is_array($payload) ? ($payload['categoryId'] ?? null) : null;
        if ((!is_string($categoryId) && !is_int($categoryId)) || '' === trim((string) $categoryId)) {
            throw new \InvalidArgumentException('Category projection event requires a categoryId.');
        }

        $category = $this->entityManager->getRepository(CatalogCategoryEntity::class)->find((string) $categoryId);
        if (!$category instanceof CatalogCategoryEntity) {
            throw new \RuntimeException(sprintf('Category %s not found for projection synchronization.', (string) $categoryId));
        }

        // The worker owns the flush, keeping projection changes and outbox
        // acknowledgement in the same Doctrine unit of work.
        $this->synchronizer->synchronize($category, false);
    }
}
