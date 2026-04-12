<?php

declare(strict_types=1);

namespace App\Service;

use App\ValueObject\CategoryProjectionCriteria;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Provides the search service application service.
 */
final readonly class SearchService
{
    private const int DEFAULT_LIMIT = 20;
    private const int MAX_LIMIT = 100;
    private const int MAX_OFFSET = 10000;

    /**
     * Initializes the search service collaborators.
     */
    public function __construct(
        private ManagerRegistry $registry,
        private CategoryProjectionQuerySupport $querySupport,
    ) {
    }

    /**
     * @return array<string,mixed>
     *
     * @throws Exception
     */
    public function search(?CategoryProjectionCriteria $criteria = null): array
    {
        $criteriaMap = $criteria?->toArray() ?? [];
        $projectionCriteria = $this->querySupport->normalizeProjectionCriteriaMap($criteriaMap);
        [$whereSql, $params, $types] = $this->querySupport->compileProjectionFilters($projectionCriteria);

        $q = $this->querySupport->optionalString($criteriaMap['q'] ?? null) ?? '';
        if ('' !== $q) {
            $whereSql .= '' === $whereSql ? ' WHERE ' : ' AND ';
            $whereSql .= '(slug LIKE :searchTerm OR name LIKE :searchTerm OR path LIKE :searchTerm)';
            $params['searchTerm'] = '%'.$q.'%';
            $types['searchTerm'] = ParameterType::STRING;
        }

        $limit = $this->boundedInt($criteriaMap['limit'] ?? null, self::DEFAULT_LIMIT, 1, self::MAX_LIMIT);
        $offset = $this->boundedInt($criteriaMap['offset'] ?? null, 0, 0, self::MAX_OFFSET);
        $order = $this->allowedString($criteriaMap['order'] ?? null, ['path', 'name', 'slug', 'updated_at'], 'path');
        $direction = $this->allowedString($criteriaMap['direction'] ?? null, ['asc', 'desc'], 'asc');

        $rows = $this->connection()->fetchAllAssociative(
            'SELECT id, slug, name, parent_id, path, locale, tenant, workflow_state, published, published_at, updated_at '
            .'FROM category_projection'.$whereSql.' ORDER BY '.$order.' '.strtoupper($direction).' LIMIT :limit OFFSET :offset',
            [...$params, 'limit' => $limit, 'offset' => $offset],
            [...$types, 'limit' => ParameterType::INTEGER, 'offset' => ParameterType::INTEGER],
        );

        $countValue = $this->connection()->fetchOne(
            'SELECT COUNT(*) FROM category_projection'.$whereSql,
            $params,
            $types,
        );

        $items = $this->querySupport->normalizeProjectionRows($rows);

        return [
            'items' => $items,
            'total' => is_numeric($countValue) ? (int) $countValue : count($items),
            'limit' => $limit,
            'offset' => $offset,
            'order' => $order,
            'direction' => $direction,
            'facets' => [
                'locale' => $this->facetCounts($this->connection(), 'locale', $whereSql, $params, $types),
                'tenant' => $this->facetCounts($this->connection(), 'tenant', $whereSql, $params, $types),
                'workflow_state' => $this->facetCounts($this->connection(), 'workflow_state', $whereSql, $params, $types),
                'published' => $this->facetCounts($this->connection(), 'published', $whereSql, $params, $types),
            ],
        ];
    }

    private function connection(): Connection
    {
        /** @var Connection $connection */
        $connection = $this->registry->getConnection('infra');

        return $connection;
    }

    /**
     * @param array<string,mixed>         $params
     * @param array<string,ParameterType> $types
     *
     * @return array<string,int>
     *
     * @throws Exception
     */
    private function facetCounts(
        Connection $connection,
        string $field,
        string $whereSql,
        array $params,
        array $types,
    ): array {
        if (!in_array($field, ['locale', 'tenant', 'workflow_state', 'published'], true)) {
            return [];
        }

        $rows = $connection->fetchAllAssociative(
            sprintf(
                'SELECT %1$s AS facet_value, COUNT(*) AS facet_count '
                .'FROM category_projection%2$s '
                .'GROUP BY %1$s '
                .'ORDER BY facet_count DESC, %1$s ASC',
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

        return $this->querySupport->optionalString($value);
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
        $normalized = strtolower($this->querySupport->optionalString($value) ?? '');

        return in_array($normalized, $allowed, true) ? $normalized : $default;
    }
}
