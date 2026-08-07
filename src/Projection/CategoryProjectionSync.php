<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Projection;

use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use App\Cataloging\ProjectionInterface\CategoryProjectionSyncInterface;
use App\Cataloging\Service\CatalogCategoryProjectionSynchronizerService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Projection sync worker that updates read models from outbox events.
 */
final readonly class CategoryProjectionSync implements CategoryProjectionSyncInterface
{
    /**
     * Initializes the category projection sync service collaborators.
     */
    public function __construct(
        private ManagerRegistry $registry,
        private CatalogCategoryProjectionSynchronizerService $synchronizer,
    ) {
    }

    /**
     * @param array<string,mixed> $event
     */
    public function apply(array $event): void
    {
        $type = $this->stringValue($event['type'] ?? null);
        if (!in_array($type, ['category.moved', 'category.published', 'category.unpublished'], true)) {
            return;
        }

        $payload = $event['payload'] ?? null;
        $categoryId = is_array($payload) ? $this->stringValue($payload['categoryId'] ?? null) : '';
        if ('' === $categoryId) {
            throw new \InvalidArgumentException('Projection event is missing categoryId.');
        }

        $entityManager = $this->entityManager();
        $category = $this->findCategoryEntity($entityManager, $categoryId);
        if (!$category instanceof CatalogCategoryEntity) {
            throw new \RuntimeException(sprintf('Projection source category "%s" was not found.', $categoryId));
        }

        $this->synchronizer->synchronize($category);
    }

    private function entityManager(): EntityManagerInterface
    {
        $manager = $this->registry->getManager();
        if (!$manager instanceof EntityManagerInterface) {
            throw new \RuntimeException('Doctrine entity manager is not available for category projection sync.');
        }

        return $manager;
    }

    private function stringValue(mixed $value): string
    {
        if (!is_scalar($value)) {
            return '';
        }

        return trim((string) $value);
    }

    private function findCategoryEntity(EntityManagerInterface $entityManager, string $id): ?CatalogCategoryEntity
    {
        $normalizedId = trim($id);
        if ('' === $normalizedId) {
            return null;
        }

        $repository = $entityManager->getRepository(CatalogCategoryEntity::class);
        if (is_numeric($normalizedId)) {
            $entity = $repository->find((int) $normalizedId);
            if ($entity instanceof CatalogCategoryEntity) {
                return $entity;
            }
        }

        $entity = $repository->findOneBy(['slug' => $normalizedId]);
        if ($entity instanceof CatalogCategoryEntity) {
            return $entity;
        }

        $entity = $repository->find($normalizedId);

        return $entity instanceof CatalogCategoryEntity ? $entity : null;
    }
}
