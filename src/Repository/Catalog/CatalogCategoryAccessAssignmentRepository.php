<?php

declare(strict_types=1);

namespace App\Cataloging\Repository\Catalog;

use App\Cataloging\Entity\Catalog\CatalogCategoryAccessAssignmentEntity;
use App\Cataloging\EntityInterface\Catalog\CatalogCategoryAccessAssignmentEntityInterface;
use App\Cataloging\RepositoryInterface\Catalog\CatalogCategoryAccessAssignmentRepositoryInterface;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

final class CatalogCategoryAccessAssignmentRepository implements CatalogCategoryAccessAssignmentRepositoryInterface
{
    /** @var array<string,CatalogCategoryAccessAssignmentEntityInterface> */
    private array $assignments = [];

    public function __construct(
        private readonly Connection|EntityManagerInterface|null $entityManager = null,
    ) {
    }

    public function save(CatalogCategoryAccessAssignmentEntityInterface $assignment): void
    {
        if ($this->entityManager instanceof EntityManagerInterface && $assignment instanceof CatalogCategoryAccessAssignmentEntity) {
            $this->entityManager->persist($assignment);
            $this->entityManager->flush();

            return;
        }

        if ($this->entityManager instanceof Connection && $assignment instanceof CatalogCategoryAccessAssignmentEntity) {
            $payload = [
                'assignment_id' => $assignment->assignmentId(),
                'category_id' => trim($assignment->categoryId()),
                'actor_user_id' => trim($assignment->actorUserId()),
                'role' => trim($assignment->role()),
                'status' => trim($assignment->status()),
                'is_primary' => $assignment->isPrimary() ? 1 : 0,
                'granted_at' => $assignment->grantedAt()->format(DATE_ATOM),
                'revoked_at' => $assignment->revokedAt()?->format(DATE_ATOM),
            ];

            $this->entityManager->insert('category_access_assignment', $payload);
            $this->assignments[$assignment->assignmentId()] = $assignment;

            return;
        }

        $this->assignments[$assignment->assignmentId()] = $assignment;
    }

    public function findPrimaryForCategoryId(string $categoryId): ?CatalogCategoryAccessAssignmentEntityInterface
    {
        $assignments = $this->findActiveByCategoryId($categoryId);

        return array_find($assignments, static fn (CatalogCategoryAccessAssignmentEntityInterface $assignment): bool => $assignment->isPrimary());
    }

    public function findActiveByCategoryId(string $categoryId): array
    {
        if ($this->entityManager instanceof EntityManagerInterface) {
            return $this->entityManager->getRepository(CatalogCategoryAccessAssignmentEntity::class)->findBy(['categoryId' => trim($categoryId), 'status' => 'active'], ['isPrimary' => 'DESC', 'grantedAt' => 'ASC']);
        }

        if ($this->entityManager instanceof Connection) {
            $rows = $this->entityManager->fetchAllAssociative(
                'SELECT * FROM category_access_assignment WHERE category_id = ? AND status = ? ORDER BY is_primary DESC, granted_at ASC',
                [trim($categoryId), 'active'],
            );

            return array_map([$this, 'hydrateEntityFromRow'], $rows);
        }

        return array_values(array_filter($this->assignments, static fn (CatalogCategoryAccessAssignmentEntityInterface $assignment): bool => $assignment->categoryId() === $categoryId && 'active' === $assignment->status()));
    }

    public function findActiveByActorUserId(string $actorUserId): array
    {
        if ($this->entityManager instanceof EntityManagerInterface) {
            return $this->entityManager->getRepository(CatalogCategoryAccessAssignmentEntity::class)->findBy(['actorUserId' => trim($actorUserId), 'status' => 'active'], ['grantedAt' => 'ASC']);
        }

        if ($this->entityManager instanceof Connection) {
            $rows = $this->entityManager->fetchAllAssociative(
                'SELECT * FROM category_access_assignment WHERE actor_user_id = ? AND status = ? ORDER BY granted_at ASC',
                [trim($actorUserId), 'active'],
            );

            return array_map([$this, 'hydrateEntityFromRow'], $rows);
        }

        return array_values(array_filter($this->assignments, static fn (CatalogCategoryAccessAssignmentEntityInterface $assignment): bool => $assignment->actorUserId() === $actorUserId && 'active' === $assignment->status()));
    }

    public function findOneByCategoryIdAndActorUserId(string $categoryId, string $actorUserId): ?CatalogCategoryAccessAssignmentEntityInterface
    {
        if ($this->entityManager instanceof EntityManagerInterface) {
            return $this->entityManager->getRepository(CatalogCategoryAccessAssignmentEntity::class)->findOneBy(['categoryId' => trim($categoryId), 'actorUserId' => trim($actorUserId)]);
        }

        if ($this->entityManager instanceof Connection) {
            $row = $this->entityManager->fetchAssociative(
                'SELECT * FROM category_access_assignment WHERE category_id = ? AND actor_user_id = ? LIMIT 1',
                [trim($categoryId), trim($actorUserId)],
            );

            return is_array($row) ? $this->hydrateEntityFromRow($row) : null;
        }

        return array_find($this->assignments, fn ($assignment) => $assignment->categoryId() === $categoryId && $assignment->actorUserId() === $actorUserId);
    }

    /**
     * @param array<string,mixed> $row
     */
    private function hydrateEntityFromRow(array $row): CatalogCategoryAccessAssignmentEntityInterface
    {
        return new CatalogCategoryAccessAssignmentEntity(
            self::stringFromMixed($row['assignment_id'] ?? ''),
            self::stringFromMixed($row['category_id'] ?? ''),
            self::stringFromMixed($row['actor_user_id'] ?? ''),
            self::stringFromMixed($row['role'] ?? ''),
            self::stringFromMixed($row['status'] ?? 'active'),
            $this->boolFromRow($row['is_primary'] ?? false),
            $this->dateTimeFromRow($row['granted_at'] ?? null) ?? new \DateTimeImmutable('now'),
            $this->dateTimeFromRow($row['revoked_at'] ?? null),
        );
    }

    private function boolFromRow(mixed $value): bool
    {
        return match (true) {
            is_bool($value) => $value,
            is_int($value) => 0 !== $value,
            is_numeric($value) => 0 !== (int) $value,
            is_string($value) => in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'on'], true),
            default => false,
        };
    }

    private function dateTimeFromRow(mixed $value): ?\DateTimeImmutable
    {
        if (!is_string($value) || '' === trim($value)) {
            return null;
        }

        return new \DateTimeImmutable($value);
    }

    private static function stringFromMixed(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
