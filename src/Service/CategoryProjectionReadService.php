<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ServiceInterface\CategoryProjectionReadServiceInterface;
use App\Cataloging\ValueObject\CategoryProjectionCriteria;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Provides the category projection read service application service.
 */
final readonly class CategoryProjectionReadService implements CategoryProjectionReadServiceInterface
{
    /**
     * Initializes the category projection read service collaborators.
     */
    public function __construct(
        private ManagerRegistry $registry,
        private CategoryProjectionQuerySupport $querySupport,
    ) {
    }

    /**
     * @return list<array<string,mixed>>
     *
     * @throws Exception
     */
    public function list(?CategoryProjectionCriteria $criteria = null): array
    {
        $criteriaMap = $this->criteriaMap($criteria);
        [$whereSql, $params, $types] = $this->querySupport->compileProjectionFilters($criteriaMap);

        $rows = $this->connection()->fetchAllAssociative(
            'SELECT id, slug, name, parent_id, path, locale, tenant, workflow_state, published, published_at, updated_at '
            .'FROM category_projection'.$whereSql.' ORDER BY path ASC, slug ASC',
            $params,
            $types,
        );

        return $this->querySupport->normalizeProjectionRows($rows);
    }

    /**
     * @return list<array<string,mixed>>
     *
     * @throws Exception
     */
    public function tree(?CategoryProjectionCriteria $criteria = null): array
    {
        $rows = $this->list($criteria);

        return $this->buildTree($rows);
    }

    /**
     * @return array<string,mixed>|null
     *
     * @throws Exception
     */
    public function findOne(string $id): ?array
    {
        $normalizedId = trim($id);
        if ('' === $normalizedId) {
            return null;
        }

        $row = $this->connection()->fetchAssociative(
            'SELECT id, slug, name, parent_id, path, locale, tenant, workflow_state, published, published_at, updated_at '
            .'FROM category_projection WHERE id = :id LIMIT 1',
            ['id' => $normalizedId],
            ['id' => ParameterType::STRING],
        );

        if (!is_array($row)) {
            return null;
        }

        $normalized = $this->querySupport->normalizeProjectionRows([$row]);

        return $normalized[0] ?? null;
    }

    /**
     * @return array{tenant: ?string, locale: ?string, workflow_state: ?string, published: ?bool}
     */
    private function criteriaMap(?CategoryProjectionCriteria $criteria): array
    {
        return $this->querySupport->normalizeProjectionCriteriaMap($criteria?->toArray() ?? []);
    }

    private function connection(): Connection
    {
        /** @var Connection $connection */
        $connection = $this->registry->getConnection('infra');

        return $connection;
    }

    /**
     * @param list<array<string,mixed>> $rows
     *
     * @return list<array<string,mixed>>
     */
    private function buildTree(array $rows): array
    {
        /** @var array<string,array<string,mixed>> $nodes */
        $nodes = [];
        foreach ($rows as $row) {
            $id = $row['id'] ?? null;
            if (!is_string($id) || '' === $id) {
                continue;
            }

            $nodes[$id] = [...$row, 'children' => []];
        }

        /** @var array<string,string> $parentIndex */
        $parentIndex = [];
        $roots = [];
        foreach ($rows as $row) {
            $id = $row['id'] ?? null;
            if (!is_string($id) || '' === $id) {
                continue;
            }
            $parentId = $row['parent_id'] ?? null;
            if (is_string($parentId) && '' !== $parentId && isset($nodes[$parentId])) {
                $parentIndex[$id] = $parentId;
                continue;
            }

            $roots[] = $id;
        }

        return $this->materializeTree($roots, $nodes, $parentIndex);
    }

    /**
     * @param list<string>                      $ids
     * @param array<string,array<string,mixed>> $nodes
     * @param array<string,string>              $parentIndex
     *
     * @return list<array<string,mixed>>
     */
    private function materializeTree(array $ids, array $nodes, array $parentIndex): array
    {
        $result = [];
        foreach ($ids as $id) {
            if (!isset($nodes[$id])) {
                continue;
            }

            $children = [];
            foreach ($parentIndex as $childId => $parentId) {
                if ($parentId === $id) {
                    $children[] = $childId;
                }
            }

            $node = $nodes[$id];
            $node['children'] = $this->materializeTree($children, $nodes, $parentIndex);
            $result[] = $node;
        }

        return $result;
    }
}
