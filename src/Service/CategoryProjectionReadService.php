<?php

declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\CategoryProjectionReadServiceInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

final class CategoryProjectionReadService implements CategoryProjectionReadServiceInterface
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly SearchService $searchService,
    ) {
    }

    public function list(array $criteria = []): array
    {
        $result = $this->searchService->search($criteria);

        return $result['items'];
    }

    public function tree(array $criteria = []): array
    {
        $normalized = $this->normalizeCriteria($criteria);
        [$whereSql, $params, $types] = $this->compileFilters($normalized);
        $rows = $this->infraConnection()->fetchAllAssociative(
            'SELECT id, slug, name, parent_id, path, locale, tenant, workflow_state, published, published_at, updated_at '
            .'FROM category_projection '
            .$whereSql
            .' ORDER BY path ASC, name ASC, id ASC',
            $params,
            $types,
        );

        return $this->buildTree($this->normalizeRows($rows));
    }

    public function findOne(string $id): ?array
    {
        $normalizedId = trim($id);
        if ('' === $normalizedId) {
            return null;
        }

        $row = $this->infraConnection()->fetchAssociative(
            'SELECT id, slug, name, parent_id, path, locale, tenant, workflow_state, published, published_at, updated_at '
            .'FROM category_projection WHERE id = :id LIMIT 1',
            ['id' => $normalizedId],
            ['id' => ParameterType::STRING],
        );

        if (!is_array($row)) {
            return null;
        }

        $rows = $this->normalizeRows([$row]);

        return $rows[0] ?? null;
    }

    private function infraConnection(): Connection
    {
        /** @var Connection $connection */
        $connection = $this->registry->getConnection('infra');

        return $connection;
    }

    /**
     * @param array<string,mixed> $criteria
     * @return array{tenant:?string,locale:?string,workflow_state:?string,published:?bool}
     */
    private function normalizeCriteria(array $criteria): array
    {
        return [
            'tenant' => $this->optionalString($criteria['tenant'] ?? null),
            'locale' => $this->optionalString($criteria['locale'] ?? null),
            'workflow_state' => $this->optionalString($criteria['workflow_state'] ?? null),
            'published' => $this->optionalBool($criteria['published'] ?? null),
        ];
    }

    /**
     * @param array{tenant:?string,locale:?string,workflow_state:?string,published:?bool} $criteria
     * @return array{0:string,1:array<string,mixed>,2:array<string,ParameterType>}
     */
    private function compileFilters(array $criteria): array
    {
        $clauses = [];
        /** @var array<string,mixed> $params */
        $params = [];
        /** @var array<string,ParameterType> $types */
        $types = [];

        if (null !== $criteria['tenant']) {
            $clauses[] = 'tenant = :tenant';
            $params['tenant'] = $criteria['tenant'];
            $types['tenant'] = ParameterType::STRING;
        }

        if (null !== $criteria['locale']) {
            $clauses[] = 'locale = :locale';
            $params['locale'] = $criteria['locale'];
            $types['locale'] = ParameterType::STRING;
        }

        if (null !== $criteria['workflow_state']) {
            $clauses[] = 'workflow_state = :workflowState';
            $params['workflowState'] = $criteria['workflow_state'];
            $types['workflowState'] = ParameterType::STRING;
        }

        if (null !== $criteria['published']) {
            $clauses[] = 'published = :published';
            $params['published'] = $criteria['published'];
            $types['published'] = ParameterType::BOOLEAN;
        }

        if ([] === $clauses) {
            return ['', [], []];
        }

        return [' WHERE '.implode(' AND ', $clauses), $params, $types];
    }

    private function optionalString(mixed $value): ?string
    {
        if (!is_scalar($value)) {
            return null;
        }

        $normalized = trim((string) $value);

        return '' === $normalized ? null : $normalized;
    }

    private function optionalBool(mixed $value): ?bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (!is_scalar($value)) {
            return null;
        }

        $normalized = strtolower(trim((string) $value));
        if (in_array($normalized, ['1', 'true', 'yes'], true)) {
            return true;
        }

        if (in_array($normalized, ['0', 'false', 'no'], true)) {
            return false;
        }

        return null;
    }

    /**
     * @param list<array<string,mixed>> $rows
     * @return list<array{
     *   id:string,
     *   slug:string,
     *   name:string,
     *   parent_id:?string,
     *   path:string,
     *   locale:string,
     *   tenant:string,
     *   workflow_state:string,
     *   published:bool,
     *   published_at:?string,
     *   updated_at:string
     * }>
     */
    private function normalizeRows(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $id = $this->optionalString($row['id'] ?? null);
            if (null === $id) {
                continue;
            }

            $result[] = [
                'id' => $id,
                'slug' => $this->optionalString($row['slug'] ?? null) ?? '',
                'name' => $this->optionalString($row['name'] ?? null) ?? '',
                'parent_id' => $this->optionalString($row['parent_id'] ?? null),
                'path' => $this->optionalString($row['path'] ?? null) ?? '',
                'locale' => $this->optionalString($row['locale'] ?? null) ?? '',
                'tenant' => $this->optionalString($row['tenant'] ?? null) ?? 'default',
                'workflow_state' => $this->optionalString($row['workflow_state'] ?? null) ?? 'draft',
                'published' => $this->optionalBool($row['published'] ?? false) ?? false,
                'published_at' => $this->optionalString($row['published_at'] ?? null),
                'updated_at' => $this->optionalString($row['updated_at'] ?? null) ?? '',
            ];
        }

        return $result;
    }

    /**
     * @param list<array{
     *   id:string,
     *   slug:string,
     *   name:string,
     *   parent_id:?string,
     *   path:string,
     *   locale:string,
     *   tenant:string,
     *   workflow_state:string,
     *   published:bool,
     *   published_at:?string,
     *   updated_at:string
     * }> $rows
     * @return list<array<string,mixed>>
     */
    private function buildTree(array $rows): array
    {
        /** @var array<string,array<string,mixed>> $nodes */
        $nodes = [];
        foreach ($rows as $row) {
            $nodes[$row['id']] = [...$row, 'children' => []];
        }

        $roots = [];
        foreach (array_keys($nodes) as $id) {
            /** @var array<string,mixed> $node */
            $node = $nodes[$id];
            $parentId = $node['parent_id'];
            if (is_string($parentId) && '' !== $parentId && isset($nodes[$parentId])) {
                /** @var array<string,mixed> $parent */
                $parent = $nodes[$parentId];
                /** @var list<array<string,mixed>> $children */
                $children = $parent['children'];
                $children[] = $node;
                $parent['children'] = $children;
                $nodes[$parentId] = $parent;
                continue;
            }

            $roots[] = $node;
        }

        return $this->rebindChildren($roots, $nodes);
    }

    /**
     * @param list<array<string,mixed>> $nodes
     * @param array<string,array<string,mixed>> $index
     * @return list<array<string,mixed>>
     */
    private function rebindChildren(array $nodes, array $index): array
    {
        $result = [];
        foreach ($nodes as $node) {
            $id = $node['id'] ?? null;
            if (!is_string($id) || !isset($index[$id])) {
                continue;
            }

            /** @var array<string,mixed> $materialized */
            $materialized = $index[$id];
            /** @var list<array<string,mixed>> $children */
            $children = $materialized['children'];
            $materialized['children'] = $this->rebindChildren($children, $index);
            $result[] = $materialized;
        }

        return $result;
    }
}
