<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;
/**
 * Provides the search service application service.
 */
final class SearchService
{
private const int DEFAULT_LIMIT = 20;
private const int MAX_LIMIT = 100;
private const int MAX_OFFSET = 10000;
    /**
     * Initializes the search service service collaborators.
     */
    public function __construct(private readonly ManagerRegistry $registry)
    {
    }

    /**
     * @param array<string,mixed> $criteria
     *
     * @return array{
     *   items:list<array{
     *     id:string,
     *     slug:string,
     *     name:string,
     *     parent_id:?string,
     *     path:string,
     *     locale:string,
     *     tenant:string,
     *     workflow_state:string,
     *     published:bool,
     *     published_at:?string,
     *     updated_at:string
     *   }>,
     *   facets:array{
     *     locale:array<string,int>,
     *     tenant:array<string,int>,
     *     workflow_state:array<string,int>,
     *     published:array<string,int>
     *   },
     *   meta:array{
     *     total:int,
     *     limit:int,
     *     offset:int,
     *     order:string,
     *     direction:string,
     *     criteria:array{
     *       q:string,
     *       tenant:?string,
     *       locale:?string,
     *       workflow_state:?string,
     *       published:?bool
     *     }
     *   }
     * }
     */
    public function search(array $criteria = []): array
    {
        $normalized = $this->normalizeCriteria($criteria);
        $connection = $this->infraConnection();
        [$whereSql, $params, $types] = $this->compileFilters($normalized);
        $orderSql = $this->orderSql($normalized['order'], $normalized['direction']);

        $selectSql = 'SELECT id, slug, name, parent_id, path, locale, tenant, '
            . 'workflow_state, published, published_at, updated_at FROM category_projection ';

        $rows = $connection->fetchAllAssociative(
            $selectSql
            . $whereSql
            . $orderSql
            . ' LIMIT :limit OFFSET :offset',
            [
                ...$params,
                'limit' => $normalized['limit'],
                'offset' => $normalized['offset'],
            ],
            [
                ...$types,
                'limit' => ParameterType::INTEGER,
                'offset' => ParameterType::INTEGER,
            ],
        );

        $total = $connection->fetchOne(
            'SELECT COUNT(*) FROM category_projection '.$whereSql,
            $params,
            $types,
        );

        return [
            'items' => $this->normalizeRows($rows),
            'facets' => [
                'locale' => $this->facetCounts($connection, 'locale', $whereSql, $params, $types),
                'tenant' => $this->facetCounts($connection, 'tenant', $whereSql, $params, $types),
                'workflow_state' => $this->facetCounts($connection, 'workflow_state', $whereSql, $params, $types),
                'published' => $this->facetCounts($connection, 'published', $whereSql, $params, $types),
            ],
            'meta' => [
                'total' => is_numeric($total) ? (int) $total : 0,
                'limit' => $normalized['limit'],
                'offset' => $normalized['offset'],
                'order' => $normalized['order'],
                'direction' => $normalized['direction'],
                'criteria' => [
                    'q' => $normalized['q'],
                    'tenant' => $normalized['tenant'],
                    'locale' => $normalized['locale'],
                    'workflow_state' => $normalized['workflow_state'],
                    'published' => $normalized['published'],
                ],
            ],
        ];
    }

    private function infraConnection(): Connection
    {
        /** @var Connection $connection */
        $connection = $this->registry->getConnection('infra');

        return $connection;
    }

    /**
     * @param array<string,mixed> $criteria
     * @return array{
     *   q:string,
     *   tenant:?string,
     *   locale:?string,
     *   workflow_state:?string,
     *   published:?bool,
     *   limit:int,
     *   offset:int,
     *   order:string,
     *   direction:string
     * }
     */
    private function normalizeCriteria(array $criteria): array
    {
        $q = $this->optionalString($criteria['q'] ?? null) ?? '';
        $tenant = $this->optionalString($criteria['tenant'] ?? null);
        $locale = $this->optionalString($criteria['locale'] ?? null);
        $workflowState = $this->optionalString($criteria['workflow_state'] ?? null);
        $published = $this->optionalBool($criteria['published'] ?? null);
        $limit = $this->boundedInt($criteria['limit'] ?? null, self::DEFAULT_LIMIT, 1, self::MAX_LIMIT);
        $offset = $this->boundedInt($criteria['offset'] ?? null, 0, 0, self::MAX_OFFSET);
        $order = $this->allowedString($criteria['order'] ?? null, ['updated_at', 'name', 'published_at'], 'updated_at');
        $direction = $this->allowedString($criteria['direction'] ?? null, ['asc', 'desc'], 'desc');

        return [
            'q' => $q,
            'tenant' => $tenant,
            'locale' => $locale,
            'workflow_state' => $workflowState,
            'published' => $published,
            'limit' => $limit,
            'offset' => $offset,
            'order' => $order,
            'direction' => $direction,
        ];
    }

    /**
     * @param array{
     *     q:string,
     *     tenant:?string,
     *     locale:?string,
     *     workflow_state:?string,
     *     published:?bool,
     *     limit:int,
     *     offset:int,
     *     order:string,
     *     direction:string,
     * } $criteria
     * @return array{0:string,1:array<string,mixed>,2:array<string,ParameterType>}
     */
    private function compileFilters(array $criteria): array
    {
        $clauses = [];
        /** @var array<string,mixed> $params */
        $params = [];
        /** @var array<string,ParameterType> $types */
        $types = [];

        if ('' !== $criteria['q']) {
            $clauses[] = '(LOWER(name) LIKE :term OR LOWER(slug) LIKE :term)';
            $params['term'] = '%'.strtolower($criteria['q']).'%';
            $types['term'] = ParameterType::STRING;
        }

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

    private function orderSql(string $order, string $direction): string
    {
        return sprintf(' ORDER BY %s %s, id ASC', $order, strtoupper($direction));
    }

    /**
     * @param list<array<string,mixed>> $rows
     * @return list<array{
     *     id:string,
     *     slug:string,
     *     name:string,
     *     parent_id:?string,
     *     path:string,
     *     locale:string,
     *     tenant:string,
     *     workflow_state:string,
     *     published:bool,
     *     published_at:?string,
     *     updated_at:string,
     * } >
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
                'published' => $this->boolValue($row['published'] ?? false),
                'published_at' => $this->optionalString($row['published_at'] ?? null),
                'updated_at' => $this->optionalString($row['updated_at'] ?? null) ?? '',
            ];
        }

        return $result;
    }

    /**
     * @param array<string,mixed> $params
     * @param array<string,ParameterType> $types
     * @return array<string,int>
     */
    private function facetCounts(
        Connection $connection,
        string $field,
        string $whereSql,
        array $params,
        array $types,
    ): array
    {
        if (!in_array($field, ['locale', 'tenant', 'workflow_state', 'published'], true)) {
            return [];
        }

        $rows = $connection->fetchAllAssociative(
            sprintf(
                'SELECT %1$s AS facet_value, COUNT(*) AS facet_count '
                . 'FROM category_projection%2$s '
                . 'GROUP BY %1$s '
                . 'ORDER BY facet_count DESC, %1$s ASC',
                $field,
                $whereSql,
            ),
            $params,
            $types,
        );

        $result = [];
        foreach ($rows as $row) {
            $key = $this->facetKey($row['facet_value'] ?? null, $field);
            if (null === $key) {
                continue;
            }

            $count = $row['facet_count'] ?? null;
            $result[$key] = is_numeric($count) ? (int) $count : 0;
        }

        return $result;
    }

    private function facetKey(mixed $value, string $field): ?string
    {
        if ('published' === $field) {
            if (is_bool($value)) {
                return $value ? 'true' : 'false';
            }
            if (is_numeric($value)) {
                return ((int) $value) === 1 ? 'true' : 'false';
            }
            if (is_string($value)) {
                $normalized = strtolower(trim($value));
                if (in_array($normalized, ['1', 'true', 'yes', 'on'], true)) {
                    return 'true';
                }
                if (in_array($normalized, ['0', 'false', 'no', 'off'], true)) {
                    return 'false';
                }
            }

            return null;
        }

        return $this->optionalString($value);
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
        return match (true) {
            is_bool($value) => $value,
            is_int($value) => 0 !== $value,
            is_string($value) => match (strtolower(trim($value))) {
                '', 'null' => null,
                '1', 'true', 'yes', 'on' => true,
                '0', 'false', 'no', 'off' => false,
                default => null,
            },
            default => null,
        };
    }

    private function boolValue(mixed $value): bool
    {
        return $this->optionalBool($value) ?? false;
    }

    private function boundedInt(mixed $value, int $default, int $min, int $max): int
    {
        if (!is_numeric($value)) {
            return $default;
        }

        return max($min, min($max, (int) $value));
    }

    /** @param list<string> $allowed */
    private function allowedString(mixed $value, array $allowed, string $default): string
    {
        $normalized = strtolower($this->optionalString($value) ?? '');

        return in_array($normalized, $allowed, true) ? $normalized : $default;
    }
}
