<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use App\Cataloging\Entity\Catalog\CatalogCategoryProjectionEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Synchronizes the durable category projection from the category write model.
 */
final readonly class CatalogCategoryProjectionSynchronizerService
{
    public function __construct(private ManagerRegistry $registry)
    {
    }

    public function synchronize(CatalogCategoryEntity $category, bool $flush = true): CatalogCategoryProjectionEntity
    {
        $entityManager = $this->entityManager();
        $projection = $entityManager->getRepository(CatalogCategoryProjectionEntity::class)->find((string) $category->getId());
        if (!$projection instanceof CatalogCategoryProjectionEntity) {
            $projection = new CatalogCategoryProjectionEntity((string) $category->getId());
            $entityManager->persist($projection);
        }

        $projection->setSlug($category->getSlug());
        $projection->setName($category->getName());
        $projection->setParentId($category->getParentId());
        $projection->setPath($category->getPath());
        $projection->setLocale($category->getLocale());
        $projection->setTenant($category->getTenant());
        $projection->setWorkflowState($category->getWorkflowState());
        $projection->setPublished($category->isPublished());
        $projection->setPublishedAt($category->getPublishedAt());
        $projection->setIconUrl($category->getIconUrl());
        $projection->setUpdatedAt(new \DateTimeImmutable('now'));

        if ($flush) {
            $entityManager->flush();
        }

        return $projection;
    }

    public function flush(): void
    {
        $this->entityManager()->flush();
    }

    private function entityManager(): EntityManagerInterface
    {
        $manager = $this->registry->getManager();
        if (!$manager instanceof EntityManagerInterface) {
            throw new \RuntimeException('Doctrine entity manager is not available for category projection synchronization.');
        }

        return $manager;
    }
}
