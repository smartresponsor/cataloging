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
            $nodePath = $this->pathFromEntity($this->entityManager->find(CatalogCategoryEntity::class, $nodeId, LockMode::PESSIMISTIC_WRITE));

            if (null !== $newParentId) {
                $parentPath = $this->pathFromEntity($this->entityManager->find(CatalogCategoryEntity::class, $newParentId, LockMode::PESSIMISTIC_WRITE));
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
}
