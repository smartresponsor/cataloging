<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Repository;

use App\Entity\CategoryEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

final class CatalogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryEntity::class);
    }

    /**
     * Direct children using Postgres ltree: path ~ (parentPath || '.*{1}').
     *
     * @return CategoryEntity[]
     */
    public function findChildrenLtree(CategoryEntity $node): array
    {
        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * FROM category WHERE path ~ ($1 || '.*{1}') ORDER BY slug ASC";
        $stmt = $conn->prepare($sql);
        $res = $stmt->executeQuery([$node->getPath()]);
        $rows = $res->fetchAllAssociative();

        return $rows ? $this->hydrate($rows) : [];
    }

    /**
     * Ancestors using Postgres ltree: path @> :childPath.
     *
     * @return CategoryEntity[]
     */
    public function findAncestorsLtree(CategoryEntity $node): array
    {
        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT * FROM category WHERE path @> $1 ORDER BY depth ASC';
        $stmt = $conn->prepare($sql);
        $res = $stmt->executeQuery([$node->getPath()]);
        $rows = $res->fetchAllAssociative();

        return $rows ? $this->hydrate($rows) : [];
    }

    /**
     * Minimal manual hydration (attributes mapping).
     *
     * @param array<int,array<string,mixed>> $rows
     *
     * @return CategoryEntity[]
     */
    private function hydrate(array $rows): array
    {
        $out = [];
        foreach ($rows as $r) {
            $e = new CategoryEntity($r['name'], $r['slug'], $r['path'], (int) $r['depth']);
            // set id via reflection (since ctor generates new one) — keep simple: assign property directly.
            $ref = new \ReflectionClass($e);
            $prop = $ref->getProperty('id');
            $prop->setAccessible(true);
            $prop->setValue($e, $r['id']);
            $out[] = $e;
        }

        return $out;
    }
}
