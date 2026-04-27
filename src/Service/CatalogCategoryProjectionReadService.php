<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\CatalogCategoryProjectionEntity;
use App\Cataloging\ServiceInterface\CatalogCategoryProjectionReadServiceInterface;
use App\Cataloging\ValueObject\CategoryProjectionCriteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Provides the category projection read service application service.
 */
final readonly class CatalogCategoryProjectionReadService implements CatalogCategoryProjectionReadServiceInterface
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
     */
    public function list(?CategoryProjectionCriteria $criteria = null): array
    {
        $criteriaMap = $this->criteriaMap($criteria);

        $entityManager = $this->entityManager();
        $entities = $entityManager->getRepository(CatalogCategoryProjectionEntity::class)->findBy([], ['path' => 'ASC', 'slug' => 'ASC']);
        $rows = [];
        foreach ($entities as $entity) {
            if (!$entity instanceof CatalogCategoryProjectionEntity) {
                continue;
            }
            $row = $this->mapEntityToRow($entity);
            if ($this->matchesCriteria($row, $criteriaMap)) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * @return list<array<string,mixed>>
     */
    public function tree(?CategoryProjectionCriteria $criteria = null): array
    {
        $rows = $this->list($criteria);

        return $this->buildTree($rows);
    }

    /**
     * @return array<string,mixed>|null
     */
    public function findOne(string $id): ?array
    {
        $normalizedId = trim($id);
        if ('' === $normalizedId) {
            return null;
        }

        $entityManager = $this->entityManager();
        $entity = $entityManager->getRepository(CatalogCategoryProjectionEntity::class)->find($normalizedId);

        return $entity instanceof CatalogCategoryProjectionEntity ? $this->mapEntityToRow($entity) : null;
    }

    /**
     * @return array{tenant: ?string, locale: ?string, workflow_state: ?string, published: ?bool}
     */
    private function criteriaMap(?CategoryProjectionCriteria $criteria): array
    {
        return $this->querySupport->normalizeProjectionCriteriaMap($criteria?->toArray() ?? []);
    }

    private function entityManager(): EntityManagerInterface
    {
        $manager = $this->registry->getManager();
        if (!$manager instanceof EntityManagerInterface) {
            throw new \RuntimeException('Doctrine entity manager is not available for category projection reads.');
        }

        return $manager;
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

    /**
     * @param array{tenant: ?string, locale: ?string, workflow_state: ?string, published: ?bool} $criteriaMap
     * @param array<string,mixed>                                                                $row
     */
    private function matchesCriteria(array $row, array $criteriaMap): bool
    {
        foreach ($criteriaMap as $field => $value) {
            if (null === $value) {
                continue;
            }

            if (($row[$field] ?? null) !== $value) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<string,mixed>
     */
    private function mapEntityToRow(CatalogCategoryProjectionEntity $entity): array
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
            'published_at' => $entity->getPublishedAt()?->format('Y-m-d H:i:s'),
            'updated_at' => $entity->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
