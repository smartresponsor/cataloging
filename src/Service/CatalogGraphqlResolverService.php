<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\CatalogCategoryProjectionEntity;
use App\Cataloging\ServiceInterface\CatalogCategoryProjectionReadServiceInterface;
use App\Cataloging\ServiceInterface\CatalogGraphqlResolverServiceInterface;
use App\Cataloging\ValueObject\CategoryGraphqlMoveRequest;
use App\Cataloging\ValueObject\CategoryGraphqlNodeRequest;
use App\Cataloging\ValueObject\CategoryGraphqlPublishRequest;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Secondary GraphQL read adapter over the canonical projection-backed catalog read model.
 *
 * This service is intentionally not the primary domain boundary. It exists as a
 * compatibility/convenience read surface and should stay thin.
 */
final readonly class CatalogGraphqlResolverService implements CatalogGraphqlResolverServiceInterface
{
    /**
     * Initializes the graphql resolver service collaborators.
     */
    public function __construct(
        private CatalogCategoryProjectionReadServiceInterface $categoryProjectionReadService,
        private ManagerRegistry $registry,
        private ?PublishOperation $publish = null,
        private ?TreeOperation $tree = null,
    ) {
    }

    /**
     * @param CategoryGraphqlNodeRequest $request
     *
     * @return array<string,mixed>|null
     */
    public function category(CategoryGraphqlNodeRequest $request): ?array
    {
        $id = $request->id();
        if ('' === $id) {
            return null;
        }

        $row = $this->categoryProjectionReadService->findOne($id);
        if (null === $row) {
            return null;
        }

        return $this->normalizeNode($row);
    }

    /**
     * @return list<array<string,mixed>>
     */
    public function categoryPath(CategoryGraphqlNodeRequest $request): array
    {
        $id = $request->id();
        if ('' === $id) {
            return [];
        }

        $row = $this->categoryProjectionReadService->findOne($id);
        if (null === $row) {
            return [];
        }

        $path = $this->stringValue($row, 'path');
        if ('' === $path) {
            return [$this->normalizeNode($row)];
        }

        $prefixes = $this->pathPrefixes($path);
        if ([] === $prefixes) {
            return [$this->normalizeNode($row)];
        }

        $rows = $this->loadPathRowsDoctrineFirst($prefixes);

        $result = [];
        foreach ($rows as $pathRow) {
            $result[] = $this->normalizeNode($pathRow);
        }

        return [] === $result ? [$this->normalizeNode($row)] : $result;
    }

    /** @return array<string,mixed>|null */
    public function publishCategory(CategoryGraphqlPublishRequest $request): ?array
    {
        $id = $request->id();
        if ('' === $id || null === $this->publish) {
            return null;
        }

        $status = new Status(Status::DRAFT);
        $published = $this->publish->publish($status);

        return [
            'id' => $id,
            'status' => $published->value(),
        ];
    }

    public function moveCategory(CategoryGraphqlMoveRequest $request): bool
    {
        $id = $request->id();
        if ('' === $id || null === $this->tree) {
            return false;
        }

        $this->tree->move($id, $request->parentId());

        return true;
    }

    /**
     * @param list<string> $prefixes
     *
     * @return list<array<string, mixed>>
     */
    private function loadPathRowsDoctrineFirst(array $prefixes): array
    {
        $rows = $this->loadPathRowsFromConnection($prefixes);
        if ([] !== $rows) {
            return $rows;
        }

        $entityManager = $this->entityManager();
        if (!$entityManager instanceof EntityManagerInterface) {
            return [];
        }

        $entities = $entityManager->createQueryBuilder()
            ->select('projection')
            ->from(CatalogCategoryProjectionEntity::class, 'projection')
            ->where('projection.path IN (:paths)')
            ->setParameter('paths', $prefixes)
            ->orderBy('projection.path', 'ASC')
            ->getQuery()
            ->getResult();

        $rows = [];
        foreach ($entities as $entity) {
            if (!$entity instanceof CatalogCategoryProjectionEntity) {
                continue;
            }

            $rows[] = [
                'id' => $entity->getId(),
                'parent_id' => $entity->getParentId(),
                'slug' => $entity->getSlug(),
                'name' => $entity->getName(),
                'locale' => $entity->getLocale(),
                'workflow_state' => $entity->getWorkflowState(),
                'published' => $entity->isPublished(),
                'path' => $entity->getPath(),
            ];
        }

        usort(
            $rows,
            static fn (array $left, array $right): int => [strlen((string) ($left['path'] ?? '')), (string) ($left['path'] ?? '')]
                <=> [strlen((string) ($right['path'] ?? '')), (string) ($right['path'] ?? '')],
        );

        return $rows;
    }

    /**
     * @param list<string> $prefixes
     *
     * @return list<array<string,mixed>>
     */
    private function loadPathRowsFromConnection(array $prefixes): array
    {
        try {
            $connection = $this->registry->getConnection('infra');
        } catch (\Throwable) {
            return [];
        }

        if (!$connection instanceof Connection) {
            return [];
        }

        $rows = $connection->fetchAllAssociative(
            'SELECT id, parent_id, slug, name, locale, workflow_state, published, path
             FROM category_projection
             WHERE path IN (?) ORDER BY path ASC',
            [$prefixes],
            [ArrayParameterType::STRING],
        );

        $normalized = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $normalized[] = [
                'id' => $row['id'] ?? '',
                'parent_id' => $row['parent_id'] ?? null,
                'slug' => $row['slug'] ?? '',
                'name' => $row['name'] ?? '',
                'locale' => $row['locale'] ?? 'en',
                'workflow_state' => $row['workflow_state'] ?? 'draft',
                'published' => $row['published'] ?? false,
                'path' => $row['path'] ?? '',
            ];
        }

        usort(
            $normalized,
            static fn (array $left, array $right): int => [strlen((string) ($left['path'] ?? '')), (string) ($left['path'] ?? '')]
                <=> [strlen((string) ($right['path'] ?? '')), (string) ($right['path'] ?? '')],
        );

        return $normalized;
    }

    private function entityManager(): ?EntityManagerInterface
    {
        try {
            $manager = $this->registry->getManager();
        } catch (\Throwable) {
            return null;
        }

        return $manager instanceof EntityManagerInterface ? $manager : null;
    }

    /**
     * @param array<string,mixed> $row
     *
     * @return array<string,mixed>
     */
    private function normalizeNode(array $row): array
    {
        $published = $this->boolValue($row['published'] ?? false);
        $workflowState = $this->stringValue($row, 'workflow_state', $published ? Status::PUBLISHED : Status::DRAFT);

        return [
            'id' => $this->stringValue($row, 'id'),
            'parentId' => $this->parentIdValue($row),
            'slug' => $this->stringValue($row, 'slug'),
            'name' => $this->stringValue($row, 'name'),
            'locale' => $this->stringValue($row, 'locale', 'en'),
            'status' => $published ? Status::PUBLISHED : $workflowState,
        ];
    }

    /** @return list<string> */
    private function pathPrefixes(string $path): array
    {
        $segments = array_values(array_filter(
            explode('.', trim($path)),
            static fn (string $segment): bool => '' !== trim($segment),
        ));
        $prefixes = [];
        $current = [];
        foreach ($segments as $segment) {
            $current[] = $segment;
            $prefixes[] = implode('.', $current);
        }

        return $prefixes;
    }

    /** @param array<string,mixed> $input */
    private function stringValue(array $input, string $key, string $default = ''): string
    {
        $value = $input[$key] ?? $default;

        return is_scalar($value) ? trim((string) $value) : $default;
    }

    /** @param array<string,mixed> $input */
    private function parentIdValue(array $input): ?string
    {
        if (!array_key_exists('parent_id', $input) || null === $input['parent_id']) {
            return null;
        }

        $value = $input['parent_id'];

        return is_scalar($value) ? trim((string) $value) : null;
    }

    private function boolValue(mixed $value): bool
    {
        return match (true) {
            is_bool($value) => $value,
            is_int($value) => 0 !== $value,
            is_string($value) => in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'on'], true),
            default => false,
        };
    }
}
