<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Repository;

use App\Entity\CategoryEntity;
use App\Service\CatalogCategoryRowNormalizer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<CategoryEntity> */
final class CatalogRepository extends ServiceEntityRepository
{
    /**
     * Initializes the catalog repository service collaborators.
     */
    public function __construct(
        ManagerRegistry $registry,
        private readonly CatalogCategoryRowNormalizer $rowNormalizer,
    ) {
        parent::__construct($registry, CategoryEntity::class);
    }

    /**
     * @return array{id:string,name:string,slug:string,path:string,depth:int}|null
     *
     * @throws Exception
     */
    public function findNodeRowById(string $id): ?array
    {
        $row = $this->getConnection()->fetchAssociative(
            'SELECT id, name, slug, path, depth FROM category WHERE id = :id LIMIT 1',
            ['id' => $id],
        );

        if (!is_array($row)) {
            return null;
        }

        $normalized = $this->rowNormalizer->normalize([$row]);

        return $normalized[0] ?? null;
    }

    /**
     * @return list<array{id:string,name:string,slug:string,path:string,depth:int}>
     *
     * @throws Exception
     */
    public function findChildrenRowsByPath(string $path): array
    {
        $rows = $this->getConnection()->executeQuery(
            "SELECT id, name, slug, path, depth FROM category WHERE path ~ (? || '.*{1}') ORDER BY slug ASC",
            [$path],
        )->fetchAllAssociative();

        return $this->rowNormalizer->normalize($rows);
    }

    /**
     * @return list<array{id:string,name:string,slug:string,path:string,depth:int}>
     *
     * @throws Exception
     */
    public function findAncestorRowsByPath(string $path): array
    {
        $rows = $this->getConnection()->executeQuery(
            'SELECT id, name, slug, path, depth FROM category WHERE path @> ? ORDER BY depth ASC',
            [$path],
        )->fetchAllAssociative();

        return $this->rowNormalizer->normalize($rows);
    }

    /**
     * @return list<array{id:string,name:string,slug:string,path:string,depth:int}>
     *
     * @throws Exception
     */
    public function findDescendantRowsByPath(string $path): array
    {
        $rows = $this->getConnection()->executeQuery(
            'SELECT id, name, slug, path, depth FROM category WHERE path <@ ? AND path <> ? ORDER BY depth ASC, slug ASC',
            [$path, $path],
        )->fetchAllAssociative();

        return $this->rowNormalizer->normalize($rows);
    }

    /**
     * @return list<array{id:string,name:string,slug:string,path:string,depth:int}>
     *
     * @throws Exception
     */
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

        return $this->rowNormalizer->normalize($rows);
    }

    private function getConnection(): Connection
    {
        return $this->getEntityManager()->getConnection();
    }
}
