<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use App\Cataloging\ServiceInterface\CatalogCategoryAdminServiceInterface;
use App\Cataloging\ServiceInterface\CatalogCategoryProjectionReadServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CatalogCategoryAdminService implements CatalogCategoryAdminServiceInterface
{
    public function __construct(
        private CatalogCategoryProjectionReadServiceInterface $projectionReadService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function list(string $query, ?string $cursor, int $limit): array
    {
        $rows = $this->projectionReadService->list(null);
        $query = trim($query);
        $filtered = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            if ('' !== $query && !$this->matchesQuery($row, $query)) {
                continue;
            }

            $filtered[] = $this->normalizeCategoryRow($row);
        }

        $offset = $this->decodeCursor($cursor);
        $limit = max(1, min(100, $limit));
        $slice = array_slice($filtered, $offset, $limit);
        $nextOffset = $offset + count($slice);
        $nextCursor = $nextOffset < count($filtered) ? $this->encodeCursor($nextOffset) : null;

        return [
            'item' => $slice,
            'nextCursor' => $nextCursor,
        ];
    }

    public function read(string $id): array
    {
        $row = $this->projectionReadService->findOne($id);
        if (null !== $row) {
            return $this->normalizeCategoryRow($row);
        }

        $entity = $this->findCategoryEntity($id);
        if ($entity instanceof CatalogCategoryEntity) {
            return $this->normalizeCategoryRow([
                'id' => $entity->getId(),
                'slug' => $entity->getSlug(),
                'nameEntity' => $entity->getName(),
                'locale' => $entity->getLocale() ?? 'en',
                'published' => $entity->isPublished(),
                'workflow_state' => $entity->getWorkflowState(),
            ]);
        }

        return [];
    }

    public function save(string $id, array $payload): array
    {
        $normalized = [
            'id' => $this->stringValue($payload['id'] ?? $id, ''),
            'slug' => $this->stringValue($payload['slug'] ?? null, ''),
            'nameEntity' => $this->stringValue($payload['nameEntity'] ?? null, ''),
            'locale' => $this->stringValue($payload['locale'] ?? 'en', 'en'),
            'status' => $this->stringValue($payload['status'] ?? 'active', 'active'),
        ];

        if ('' === $normalized['slug'] || '' === $normalized['nameEntity']) {
            throw new \InvalidArgumentException('CategoryEntity slug and nameEntity are required.');
        }

        $repository = $this->entityManager->getRepository(CatalogCategoryEntity::class);
        $entity = null;
        if ('' !== $normalized['id'] && 'new' !== $normalized['id']) {
            $candidate = $this->findCategoryEntity($normalized['id']);
            if ($candidate instanceof CatalogCategoryEntity) {
                $entity = $candidate;
            }
        }

        if (!$entity instanceof CatalogCategoryEntity) {
            $entity = new CatalogCategoryEntity(
                $normalized['nameEntity'],
                $normalized['slug'],
                $normalized['slug'],
                0,
                null,
                $normalized['locale'],
            );
            $this->entityManager->persist($entity);
        } else {
            $entity->setName($normalized['nameEntity']);
            $entity->setSlug($normalized['slug']);
            $entity->setLocale($normalized['locale']);
        }

        $published = 'active' === strtolower($normalized['status']);
        $entity->setPublished($published);
        $entity->setWorkflowState($published ? 'published' : 'draft');
        $this->entityManager->flush();

        return $this->normalizeCategoryRow([
            'id' => $entity->getId(),
            'slug' => $entity->getSlug(),
            'nameEntity' => $entity->getName(),
            'locale' => $entity->getLocale() ?? 'en',
            'published' => $entity->isPublished(),
            'workflow_state' => $entity->getWorkflowState(),
        ]);
    }

    /**
     * @param array<string, mixed> $row
     *
     * @return array{id:int,slug:string,name:string,locale:string,status:string}
     */
    private function normalizeCategoryRow(array $row): array
    {
        $published = (bool) ($row['published'] ?? false);
        $workflowState = $this->stringValue($row['workflow_state'] ?? null, $published ? 'published' : 'draft');

        return [
            'id' => $this->intValue($row['id'] ?? null),
            'slug' => $this->stringValue($row['slug'] ?? null, ''),
            'nameEntity' => $this->stringValue($row['nameEntity'] ?? null, ''),
            'locale' => $this->stringValue($row['locale'] ?? null, 'en'),
            'status' => $published ? 'active' : ('published' === $workflowState ? 'active' : 'draft'),
        ];
    }

    private function matchesQuery(array $row, string $query): bool
    {
        $needle = mb_strtolower($query);
        foreach (['id', 'slug', 'nameEntity', 'locale'] as $field) {
            $value = $this->stringValue($row[$field] ?? null, '');
            if ('' !== $value && str_contains(mb_strtolower($value), $needle)) {
                return true;
            }
        }

        return false;
    }

    private function decodeCursor(?string $cursor): int
    {
        if (null === $cursor || '' === trim($cursor)) {
            return 0;
        }

        $decoded = base64_decode($cursor, true);
        if (false === $decoded || !is_numeric($decoded)) {
            return 0;
        }

        return max(0, (int) $decoded);
    }

    private function encodeCursor(int $offset): string
    {
        return base64_encode((string) max(0, $offset));
    }

    private function stringValue(mixed $value, string $default): string
    {
        if (is_scalar($value)) {
            $normalized = trim((string) $value);

            return '' === $normalized ? $default : $normalized;
        }

        return $default;
    }

    private function findCategoryEntity(string $id): ?CatalogCategoryEntity
    {
        $normalizedId = trim($id);
        if ('' === $normalizedId) {
            return null;
        }

        $repository = $this->entityManager->getRepository(CatalogCategoryEntity::class);
        if (is_numeric($normalizedId)) {
            $entity = $repository->find((int) $normalizedId);

            return $entity instanceof CatalogCategoryEntity ? $entity : null;
        }

        $entity = $repository->findOneBy(['slug' => $normalizedId]);
        if ($entity instanceof CatalogCategoryEntity) {
            return $entity;
        }

        $entity = $repository->find($normalizedId);

        return $entity instanceof CatalogCategoryEntity ? $entity : null;
    }

    private function intValue(mixed $value, int $default = 0): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_scalar($value) && is_numeric((string) $value)) {
            return (int) $value;
        }

        return $default;
    }
}
