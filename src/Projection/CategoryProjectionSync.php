<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Projection;

use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use App\Cataloging\Entity\Catalog\CatalogCategoryProjectionEntity;
use App\Cataloging\ProjectionInterface\CategoryProjectionSyncInterface;
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
    public function __construct(private ManagerRegistry $registry)
    {
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
        $category = $entityManager->getRepository(CatalogCategoryEntity::class)->find($categoryId);
        if (!$category instanceof CatalogCategoryEntity) {
            throw new \RuntimeException(sprintf('Projection source category "%s" was not found.', $categoryId));
        }

        $projection = $entityManager->getRepository(CatalogCategoryProjectionEntity::class)->find($categoryId);
        if (!$projection instanceof CatalogCategoryProjectionEntity) {
            $projection = new CatalogCategoryProjectionEntity($category->getId());
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
        $projection->setUpdatedAt(new \DateTimeImmutable('now'));

        $entityManager->flush();
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
}
