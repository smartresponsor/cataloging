<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Repository;

use App\Cataloging\Entity\CatalogCategoryEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<CatalogCategoryEntity> */
final class CatalogRepository extends ServiceEntityRepository
{
    /**
     * Initializes the catalog repository service collaborators.
     */
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, CatalogCategoryEntity::class);
    }

    /**
     * @return array{id:string,name:string,slug:string,path:string,depth:int}|null
     */
    public function findNodeRowById(string $id): ?array
    {
        $entity = $this->find($id);

        return $entity instanceof CatalogCategoryEntity ? $this->rowFromEntity($entity) : null;
    }

    /**
     * @return list<array{id:string,name:string,slug:string,path:string,depth:int}>
     */
    public function findChildrenRowsByPath(string $path): array
    {
        $entities = $this->createQueryBuilder('c')
            ->where('c.depth = :depth')
            ->andWhere('c.path LIKE :pathPattern')
            ->setParameter('depth', $this->pathDepth($path) + 1)
            ->setParameter('pathPattern', $path.'.%')
            ->orderBy('c.slug', 'ASC')
            ->getQuery()
            ->getResult();

        return is_array($entities) ? $this->rowsFromEntities($entities) : [];
    }

    /**
     * @return list<array{id:string,name:string,slug:string,path:string,depth:int}>
     */
    public function findAncestorRowsByPath(string $path): array
    {
        $entities = $this->createQueryBuilder('c')
            ->where('c.path = :path OR :path LIKE CONCAT(c.path, , ' % ') OR c.path = :path')
            ->setParameter('path', $path)
            ->orderBy('c.depth', 'ASC')
            ->getQuery()
            ->getResult();

        if (!is_array($entities)) {
            return [];
        }

        return array_values(array_filter(
            $this->rowsFromEntities($entities),
            fn (array $row): bool => $path === $row['path'] || str_starts_with($path, $row['path'].'.')
        ));
    }

    /**
     * @return list<array{id:string,name:string,slug:string,path:string,depth:int}>
     */
    public function findDescendantRowsByPath(string $path): array
    {
        $entities = $this->createQueryBuilder('c')
            ->where('c.path LIKE :pathPattern')
            ->andWhere('c.path <> :path')
            ->setParameter('pathPattern', $path.'.%')
            ->setParameter('path', $path)
            ->orderBy('c.depth', 'ASC')
            ->addOrderBy('c.slug', 'ASC')
            ->getQuery()
            ->getResult();

        return is_array($entities) ? $this->rowsFromEntities($entities) : [];
    }

    /**
     * @return list<array{id:string,name:string,slug:string,path:string,depth:int}>
     */
    public function findPageRows(int $limit, string $after): array
    {
        $normalizedAfter = '' !== $after ? (base64_decode($after, true) ?: '') : '';

        $queryBuilder = $this->createQueryBuilder('c')
            ->orderBy('c.path', 'ASC')
            ->setMaxResults($limit);

        if ('' !== $normalizedAfter) {
            $queryBuilder->where('c.path > :cursor')->setParameter('cursor', $normalizedAfter);
        }

        $entities = $queryBuilder->getQuery()->getResult();

        return is_array($entities) ? $this->rowsFromEntities($entities) : [];
    }

    /**
     * @param list<CatalogCategoryEntity> $entities
     *
     * @return list<array{id:string,name:string,slug:string,path:string,depth:int}>
     */
    private function rowsFromEntities(array $entities): array
    {
        return array_map(fn (CatalogCategoryEntity $entity): array => $this->rowFromEntity($entity), $entities);
    }

    /**
     * @return array{id:string,name:string,slug:string,path:string,depth:int}
     */
    private function rowFromEntity(CatalogCategoryEntity $entity): array
    {
        return [
            'id' => $entity->getId(),
            'name' => $entity->getName(),
            'slug' => $entity->getSlug(),
            'path' => $entity->getPath(),
            'depth' => $entity->getDepth(),
        ];
    }

    private function pathDepth(string $path): int
    {
        if ('' === trim($path)) {
            return 0;
        }

        return substr_count($path, '.');
    }
}
