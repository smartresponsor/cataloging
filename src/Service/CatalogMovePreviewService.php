<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Entity\CategoryEntity;
use App\ServiceInterface\CatalogMovePreviewServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

final class CatalogMovePreviewService implements CatalogMovePreviewServiceInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function preview(string $sourceId, string $targetParentId): ?array
    {
        /** @var CategoryEntity|null $source */
        $source = $this->entityManager->getRepository(CategoryEntity::class)->find($sourceId);
        /** @var CategoryEntity|null $target */
        $target = $this->entityManager->getRepository(CategoryEntity::class)->find($targetParentId);
        if (null === $source || null === $target) {
            return null;
        }

        $newPath = $target->getPath().'.'.basename(str_replace('.', '/', $source->getPath()));
        $newDepth = $target->getDepth() + 1;
        $duplicate = $this->entityManager->getRepository(CategoryEntity::class)->findOneBy([
            'slug' => $source->getSlug(),
            'depth' => $newDepth,
        ]);

        return [
            'newPath' => $newPath,
            'newDepth' => $newDepth,
            'conflict' => null !== $duplicate,
        ];
    }
}
