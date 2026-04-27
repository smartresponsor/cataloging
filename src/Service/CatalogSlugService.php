<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\CatalogCategoryEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Provides the slug service application service.
 */
final readonly class CatalogCatalogSlugService
{
    /**
     * Initializes the slug service service collaborators.
     */
    public function __construct(
        private ManagerRegistry $registry,
    ) {
    }

    /**
     * Handles the ensure unique workflow.
     *
     * @throws Exception
     */
    public function ensureUnique(string $slug): string
    {
        $baseSlug = $slug;
        $suffix = 2;
        while ($this->exists($slug)) {
            $slug = $baseSlug.'-'.$suffix;
            ++$suffix;
        }

        return $slug;
    }

    private function categoryEntityManager(): ?EntityManagerInterface
    {
        $manager = $this->registry->getManagerForClass(CatalogCategoryEntity::class);

        return $manager instanceof EntityManagerInterface ? $manager : null;
    }

    private function exists(string $slug): bool
    {
        $entityManager = $this->categoryEntityManager();
        if (!$entityManager instanceof EntityManagerInterface) {
            return false;
        }

        $entity = $entityManager->getRepository(CatalogCategoryEntity::class)->findOneBy(['slug' => $slug]);

        return $entity instanceof CatalogCategoryEntity;
    }
}
