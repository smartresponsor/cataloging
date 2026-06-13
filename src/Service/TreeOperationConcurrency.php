<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;

final class TreeOperationConcurrency
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CatalogTreeLockService $treeLock,
    ) {
    }

    public function move(string $nodeId, ?string $newParentId): void
    {
        if ($nodeId === $newParentId) {
            throw new \InvalidArgumentException('Node cannot be parent of itself');
        }

        $this->treeLock->acquire('category_tree');
        try {
            $this->entityManager->beginTransaction();
            $nodePath = $this->pathFromEntity($this->findLockedCategoryEntity($nodeId));

            if (null !== $newParentId) {
                $parentPath = $this->pathFromEntity($this->findLockedCategoryEntity($newParentId));
                if ('' !== $parentPath && '' !== $nodePath && str_starts_with($parentPath, $nodePath)) {
                    throw new \InvalidArgumentException('Cycle detected');
                }
            }

            $this->entityManager->commit();
        } catch (\Throwable $exception) {
            if ($this->entityManager->getConnection()->isTransactionActive()) {
                $this->entityManager->rollback();
            }
            if ($exception instanceof \InvalidArgumentException || $exception instanceof \RuntimeException) {
                throw $exception;
            }
            throw new \RuntimeException('Tree move failed: '.$exception->getMessage(), 0, $exception);
        } finally {
            $this->treeLock->release('category_tree');
        }
    }

    private function pathFromEntity(?CatalogCategoryEntity $entity): string
    {
        return $entity instanceof CatalogCategoryEntity ? $entity->getPath() : '';
    }

    private function findLockedCategoryEntity(string $id): ?CatalogCategoryEntity
    {
        $normalizedId = trim($id);
        if ('' === $normalizedId) {
            return null;
        }

        $repository = $this->entityManager->getRepository(CatalogCategoryEntity::class);
        if (is_numeric($normalizedId)) {
            $entity = $this->entityManager->find(CatalogCategoryEntity::class, (int) $normalizedId, LockMode::PESSIMISTIC_WRITE);

            return $entity instanceof CatalogCategoryEntity ? $entity : null;
        }

        $candidate = $repository->findOneBy(['slug' => $normalizedId]);
        if ($candidate instanceof CatalogCategoryEntity) {
            $entity = $this->entityManager->find(CatalogCategoryEntity::class, $candidate->getId(), LockMode::PESSIMISTIC_WRITE);

            return $entity instanceof CatalogCategoryEntity ? $entity : null;
        }

        $entity = $this->entityManager->find(CatalogCategoryEntity::class, $normalizedId, LockMode::PESSIMISTIC_WRITE);

        return $entity instanceof CatalogCategoryEntity ? $entity : null;
    }
}
