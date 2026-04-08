<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Repository;

use App\Entity\CategoryEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<CategoryEntity> */
final class CatalogRepository extends ServiceEntityRepository
{
    /**
     * Initializes the catalog repository service collaborators.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryEntity::class);
    }

    /** @return array{id:string,name:string,slug:string,path:string,depth:int}|null */
    public function findNodeRowById(string $id): ?array
    {
        $row = $this->getConnection()->fetchAssociative(
            'SELECT id, name, slug, path, depth FROM category WHERE id = :id LIMIT 1',
            ['id' => $id],
        );

        if (!is_array($row)) {
            return null;
        }

        $normalized = $this->normalizeRows([$row]);

        return $normalized[0] ?? null;
    }

    /** @return list<array{id:string,name:string,slug:string,path:string,depth:int}> */
    public function findChildrenRowsByPath(string $path): array
    {
        $rows = $this->getConnection()->executeQuery(
            "SELECT id, name, slug, path, depth FROM category WHERE path ~ (? || '.*{1}') ORDER BY slug ASC",
            [$path],
        )->fetchAllAssociative();

        return $this->normalizeRows($rows);
    }

    /** @return list<array{id:string,name:string,slug:string,path:string,depth:int}> */
    public function findAncestorRowsByPath(string $path): array
    {
        $rows = $this->getConnection()->executeQuery(
            'SELECT id, name, slug, path, depth FROM category WHERE path @> ? ORDER BY depth ASC',
            [$path],
        )->fetchAllAssociative();

        return $this->normalizeRows($rows);
    }

    /** @return list<array{id:string,name:string,slug:string,path:string,depth:int}> */
    public function findDescendantRowsByPath(string $path): array
    {
        $rows = $this->getConnection()->executeQuery(
            'SELECT id, name, slug, path, depth FROM category WHERE path <@ ? AND path <> ? ORDER BY depth ASC, slug ASC',
            [$path, $path],
        )->fetchAllAssociative();

        return $this->normalizeRows($rows);
    }

    /** @return list<array{id:string,name:string,slug:string,path:string,depth:int}> */
    public function findPageRows(int $limit, string $after): array
    {
        $normalizedAfter = '' !== $after ? (base64_decode($after, true) ?: '') : '';
        $params = [];
        $types = [];
        $sql = 'SELECT id, name, slug, path, depth FROM category';

        if ('' !== $normalizedAfter) {
            $sql .= ' WHERE path > :cursor';
            $params['cursor'] = $normalizedAfter;
            $types['cursor'] = ParameterType::STRING;
        }

        $sql .= ' ORDER BY path ASC LIMIT :limit';
        $params['limit'] = $limit;
        $types['limit'] = ParameterType::INTEGER;

        $rows = $this->getConnection()->executeQuery($sql, $params, $types)->fetchAllAssociative();

        return $this->normalizeRows($rows);
    }

    /**
     * @param list<array<string,mixed>> $rows
     *
     * @return list<array{id:string,name:string,slug:string,path:string,depth:int}>
     */
    private function normalizeRows(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'id' => is_scalar($row['id'] ?? null) ? (string) $row['id'] : '',
                'name' => is_scalar($row['name'] ?? null) ? (string) $row['name'] : '',
                'slug' => is_scalar($row['slug'] ?? null) ? (string) $row['slug'] : '',
                'path' => is_scalar($row['path'] ?? null) ? (string) $row['path'] : '',
                'depth' => is_numeric($row['depth'] ?? null) ? (int) $row['depth'] : 0,
            ];
        }

        return $result;
    }

    private function getConnection(): Connection
    {
        return $this->getEntityManager()->getConnection();
    }
}
