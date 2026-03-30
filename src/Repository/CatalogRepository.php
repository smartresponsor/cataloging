<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Repository;

use App\Entity\CategoryEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<CategoryEntity> */
final class CatalogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryEntity::class);
    }

    /**
     * Direct children using Postgres ltree: path ~ (parentPath || '.*{1}').
     *
     * @return list<CategoryEntity>
     */
    public function findChildrenLtree(CategoryEntity $node): array
    {
        $conn = $this->getConnection();
        $sql = "SELECT * FROM category WHERE path ~ (? || '.*{1}') ORDER BY slug ASC";
        $res = $conn->executeQuery($sql, [$node->getPath()]);
        $rows = $res->fetchAllAssociative();

        return [] !== $rows ? $this->hydrate($rows) : [];
    }

    /**
     * Ancestors using Postgres ltree: path @> :childPath.
     *
     * @return list<CategoryEntity>
     */
    public function findAncestorsLtree(CategoryEntity $node): array
    {
        $conn = $this->getConnection();
        $sql = 'SELECT * FROM category WHERE path @> ? ORDER BY depth ASC';
        $res = $conn->executeQuery($sql, [$node->getPath()]);
        $rows = $res->fetchAllAssociative();

        return [] !== $rows ? $this->hydrate($rows) : [];
    }

    /**
     * Descendants using Postgres ltree: path <@ parentPath and skip the node itself.
     *
     * @return list<CategoryEntity>
     */
    public function findDescendantsLtree(CategoryEntity $node): array
    {
        $conn = $this->getConnection();
        $sql = 'SELECT * FROM category WHERE path <@ ? AND path <> ? ORDER BY depth ASC, slug ASC';
        $res = $conn->executeQuery($sql, [$node->getPath(), $node->getPath()]);
        $rows = $res->fetchAllAssociative();

        return [] !== $rows ? $this->hydrate($rows) : [];
    }

    /**
     * Minimal manual hydration (attributes mapping).
     *
     * @param list<array<string,mixed>> $rows
     *
     * @return list<CategoryEntity>
     */
    private function hydrate(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $entity = new CategoryEntity(
                is_scalar($row['name'] ?? null) ? (string) $row['name'] : '',
                is_scalar($row['slug'] ?? null) ? (string) $row['slug'] : '',
                is_scalar($row['path'] ?? null) ? (string) $row['path'] : '',
                is_numeric($row['depth'] ?? null) ? (int) $row['depth'] : 0,
            );

            $ref = new \ReflectionClass($entity);
            $prop = $ref->getProperty('id');
            $prop->setAccessible(true);
            $prop->setValue($entity, is_scalar($row['id'] ?? null) ? (string) $row['id'] : '');
            $result[] = $entity;
        }

        return $result;
    }

    private function getConnection(): Connection
    {
        return $this->getEntityManager()->getConnection();
    }
}
