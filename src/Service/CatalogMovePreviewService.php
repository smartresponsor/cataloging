<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use App\Cataloging\ServiceInterface\CatalogMovePreviewServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Provides the catalog move preview service application service.
 */
final readonly class CatalogMovePreviewService implements CatalogMovePreviewServiceInterface
{
    /**
     * Initializes the catalog move preview service service collaborators.
     */
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * Handles the preview workflow.
     */
    public function preview(string $sourceId, string $targetParentId): ?array
    {
        $source = $this->findCategoryEntity($sourceId);
        $target = $this->findCategoryEntity($targetParentId);
        if (null === $source || null === $target) {
            return null;
        }

        $newPath = $target->getPath().'.'.basename(str_replace('.', '/', $source->getPath()));
        $newDepth = $target->getDepth() + 1;
        $duplicate = $this->entityManager->getRepository(CatalogCategoryEntity::class)->findOneBy([
            'slug' => $source->getSlug(),
            'depth' => $newDepth,
        ]);

        return [
            'newPath' => $newPath,
            'newDepth' => $newDepth,
            'conflict' => null !== $duplicate,
        ];
    }

    private function findCategoryEntity(string $id): ?CatalogCategoryEntity
    {
        $normalizedId = trim($id);
        if ('' === $normalizedId) {
            return null;
        }

        $repository = $this->entityManager->getRepository(CatalogCategoryEntity::class);
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
