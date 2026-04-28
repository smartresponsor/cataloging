<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Repository\Catalog;

use App\Cataloging\Entity\Catalog\CatalogRecordIndexEntity;
use App\Cataloging\RepositoryInterface\Catalog\CatalogCollectionProjectionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Provides repository services for catalog collection projection repository.
 */
final readonly class CatalogCollectionProjectionRepository implements CatalogCollectionProjectionRepositoryInterface
{
    /**
     * Initializes the catalog collection projection repository service collaborators.
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Handles the list workflow.
     */
    public function list(): array
    {
        /** @var list<CatalogRecordIndexEntity> $entities */
        $entities = $this->entityManager->createQueryBuilder()
            ->select('recordIndex')
            ->from(CatalogRecordIndexEntity::class, 'recordIndex')
            ->orderBy('recordIndex.id', 'ASC')
            ->getQuery()
            ->getResult();

        return array_values(array_filter(array_map(
            fn (CatalogRecordIndexEntity $entity): ?array => $this->normalizeRecordIndexEntity($entity),
            $entities,
        )));
    }

    /**
     * @return array{id:string,brand:?string,price:?float,stock:?int,tag_set?:list<bool|float|int|string>}|null
     */
    private function normalizeRecordIndexEntity(CatalogRecordIndexEntity $entity): ?array
    {
        $id = $entity->getId();
        if ('' === $id) {
            return null;
        }

        $item = [
            'id' => $id,
            'brand' => $entity->getBrand(),
            'price' => $entity->getPrice(),
            'stock' => $entity->getStock(),
        ];

        $tagSet = $entity->getTagSet();
        if (is_array($tagSet) && [] !== $tagSet) {
            $item['tag_set'] = $tagSet;
        }

        return $item;
    }
}
