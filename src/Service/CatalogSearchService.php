<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\Catalog\CatalogCategoryProjectionEntity;
use App\Cataloging\ValueObject\CategoryProjectionCriteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * Provides the search service application service.
 *
 * category_projection-backed read model.
 */
final readonly class CatalogSearchService
{
    private const int DEFAULT_LIMIT = 20;
    private const int MAX_LIMIT = 100;
    private const int MAX_OFFSET = 10000;

    /**
     * Initializes the search service collaborators.
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CatalogCategoryProjectionQuerySupportService $querySupport,
    ) {
    }

    /**
     * @return array<string,mixed>
     */
    public function search(?CategoryProjectionCriteria $criteria = null): array
    {
        $criteriaMap = $criteria?->toArray() ?? [];
        $limit = $this->boundedInt($criteriaMap['limit'] ?? null, self::DEFAULT_LIMIT, 1, self::MAX_LIMIT);
        $offset = $this->boundedInt($criteriaMap['offset'] ?? null, 0, 0, self::MAX_OFFSET);
        $order = $this->allowedString($criteriaMap['order'] ?? null, ['path', 'name', 'slug', 'updated_at'], 'path');
        $direction = strtoupper($this->allowedString($criteriaMap['direction'] ?? null, ['asc', 'desc'], 'asc'));

        return $this->searchOrmOnly($criteriaMap, $limit, $offset, $order, $direction);
    }

    /**
     * @param array<string,mixed> $criteriaMap
     *
     * @return array<string,mixed>
     */
    private function searchOrmOnly(array $criteriaMap, int $limit, int $offset, string $order, string $direction): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select('projection')
            ->from(CatalogCategoryProjectionEntity::class, 'projection');

        $this->applyOrmProjectionFilters($queryBuilder, $criteriaMap);

        $orderField = match ($order) {
            'name' => 'projection.name',
            'slug' => 'projection.slug',
            'updated_at' => 'projection.updatedAt',
            default => 'projection.path',
        };

        $queryBuilder
            ->orderBy($orderField, $direction)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        /** @var list<CatalogCategoryProjectionEntity> $entities */
        $entities = $queryBuilder->getQuery()->getResult();
        $items = array_map(fn (CatalogCategoryProjectionEntity $entity): array => $this->normalizeProjectionEntity($entity), $entities);

        $countBuilder = $this->entityManager->createQueryBuilder();
        $countBuilder
            ->select('COUNT(projection.id)')
            ->from(CatalogCategoryProjectionEntity::class, 'projection');
        $this->applyOrmProjectionFilters($countBuilder, $criteriaMap);

        $totalValue = $countBuilder->getQuery()->getSingleScalarResult();
        $total = is_numeric($totalValue) ? (int) $totalValue : count($items);

        return [
            'items' => $items,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
            'order' => strtolower($order),
            'direction' => strtolower($direction),
            'facets' => [
                'locale' => $this->facetCountsOrm('locale', $criteriaMap),
                'tenant' => $this->facetCountsOrm('tenant', $criteriaMap),
                'workflow_state' => $this->facetCountsOrm('workflow_state', $criteriaMap),
                'published' => $this->facetCountsOrm('published', $criteriaMap),
            ],
        ];
    }

    /**
     * @param array<string,mixed> $criteriaMap
     */
    private function applyOrmProjectionFilters(QueryBuilder $queryBuilder, array $criteriaMap): void
    {
        $projectionCriteria = $this->querySupport->normalizeProjectionCriteriaMap($criteriaMap);

        if (null !== $projectionCriteria['tenant']) {
            $queryBuilder
                ->andWhere('projection.tenant = :tenant')
                ->setParameter('tenant', $projectionCriteria['tenant']);
        }

        if (null !== $projectionCriteria['locale']) {
            $queryBuilder
                ->andWhere('projection.locale = :locale')
                ->setParameter('locale', $projectionCriteria['locale']);
        }

        if (null !== $projectionCriteria['workflow_state']) {
            $queryBuilder
                ->andWhere('projection.workflowState = :workflowState')
                ->setParameter('workflowState', $projectionCriteria['workflow_state']);
        }

        if (null !== $projectionCriteria['published']) {
            $queryBuilder
                ->andWhere('projection.published = :published')
                ->setParameter('published', $projectionCriteria['published']);
        }

        $q = $this->querySupport->optionalString($criteriaMap['q'] ?? null) ?? '';
        if ('' !== $q) {
            $queryBuilder
                ->andWhere('(projection.slug LIKE :searchTerm OR projection.name LIKE :searchTerm OR projection.path LIKE :searchTerm)')
                ->setParameter('searchTerm', '%'.$q.'%');
        }
    }

    /**
     * @param array<string,mixed> $criteriaMap
     *
     * @return array<string,int>
     */
    private function facetCountsOrm(string $field, array $criteriaMap): array
    {
        if (!in_array($field, ['locale', 'tenant', 'workflow_state', 'published'], true)) {
            return [];
        }

        $selectField = match ($field) {
            'workflow_state' => 'projection.workflowState',
            default => 'projection.'.$field,
        };

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select($selectField.' AS facetValue, COUNT(projection.id) AS facetCount')
            ->from(CatalogCategoryProjectionEntity::class, 'projection');

        $this->applyOrmProjectionFilters($queryBuilder, $criteriaMap);

        $queryBuilder
            ->groupBy($selectField)
            ->orderBy('facetCount', 'DESC')
            ->addOrderBy('facetValue', 'ASC');

        /** @var list<array{facetValue:mixed, facetCount:mixed}> $rows */
        $rows = $queryBuilder->getQuery()->getArrayResult();
        $result = [];

        foreach ($rows as $row) {
            $key = $this->facetKey($row['facetValue'] ?? null, $field);
            if (null === $key) {
                continue;
            }

            $count = $row['facetCount'] ?? null;
            $result[$key] = is_numeric($count) ? (int) $count : 0;
        }

        return $result;
    }

    /**
     * @return array{
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
     * }
     */
    private function normalizeProjectionEntity(CatalogCategoryProjectionEntity $entity): array
    {
        return [
            'id' => $entity->getId(),
            'slug' => $entity->getSlug(),
            'name' => $entity->getName(),
            'parent_id' => $entity->getParentId(),
            'path' => $entity->getPath(),
            'locale' => $entity->getLocale() ?? '',
            'tenant' => $entity->getTenant(),
            'workflow_state' => $entity->getWorkflowState(),
            'published' => $entity->isPublished(),
            'published_at' => $entity->getPublishedAt()?->format(DATE_ATOM),
            'updated_at' => $entity->getUpdatedAt()->format(DATE_ATOM),
        ];
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
