<?php

declare(strict_types=1);

namespace App\Cataloging\Repository;

use App\Cataloging\Entity\CatalogVirtualCategoryEntity;
use App\Cataloging\RepositoryInterface\VirtualCategoryRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class VirtualCategoryRepository implements VirtualCategoryRepositoryInterface
{
    public function __construct(private ?EntityManagerInterface $entityManager = null)
    {
    }

    public function findById(string $id): ?array
    {
        $normalizedId = trim($id);
        if ('' === $normalizedId || !$this->entityManager instanceof EntityManagerInterface) {
            return null;
        }

        $entity = $this->entityManager->find(CatalogVirtualCategoryEntity::class, $normalizedId);
        if (!$entity instanceof CatalogVirtualCategoryEntity) {
            return null;
        }

        return ['id' => $entity->getId(), 'name' => $entity->getName(), 'rule' => $entity->getRule()];
    }
}
