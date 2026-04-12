<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Repository;

use App\Entity\CategoryAccessAssignment;
use App\EntityInterface\CategoryAccessAssignmentInterface;
use App\RepositoryInterface\CategoryAccessAssignmentRepositoryInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

/**
 * Provides repository services for category access assignment repository.
 */
final class CategoryAccessAssignmentRepository implements CategoryAccessAssignmentRepositoryInterface
{
    /** @var array<string,CategoryAccessAssignmentInterface> */
    private array $assignments = [];

    /**
     * Initializes the category access assignment repository service collaborators.
     */
    public function __construct(private readonly ?Connection $connection = null)
    {
    }

    /**
     * Handles the save workflow.
     *
     * @throws \Throwable
     */
    public function save(CategoryAccessAssignmentInterface $assignment): void
    {
        if (!$this->connection instanceof Connection) {
            $this->assignments[$assignment->assignmentId()] = $assignment;

            return;
        }

        $existing = $this->connection->fetchOne(
            'SELECT assignment_id FROM category_access_assignment '
            .'WHERE category_id = :categoryId AND actor_user_id = :actorUserId',
            [
                'categoryId' => $assignment->categoryId(),
                'actorUserId' => $assignment->actorUserId(),
            ],
            ['categoryId' => ParameterType::STRING, 'actorUserId' => ParameterType::STRING],
        );

        $payload = [
            'assignment_id' => $assignment->assignmentId(),
            'category_id' => $assignment->categoryId(),
            'actor_user_id' => $assignment->actorUserId(),
            'role' => $assignment->role(),
            'status' => $assignment->status(),
            'is_primary' => $assignment->isPrimary() ? 1 : 0,
            'granted_at' => $assignment->grantedAt()->format('Y-m-d H:i:s'),
            'revoked_at' => $assignment->revokedAt()?->format('Y-m-d H:i:s'),
        ];

        if (is_string($existing) && '' !== trim($existing)) {
            $this->connection->update(
                'category_access_assignment',
                $payload,
                ['assignment_id' => $existing],
                [
                    'assignment_id' => ParameterType::STRING,
                    'category_id' => ParameterType::STRING,
                    'actor_user_id' => ParameterType::STRING,
                    'role' => ParameterType::STRING,
                    'status' => ParameterType::STRING,
                    'is_primary' => ParameterType::INTEGER,
                    'granted_at' => ParameterType::STRING,
                    'revoked_at' => null === $payload['revoked_at'] ? ParameterType::NULL : ParameterType::STRING,
                ],
            );

            return;
        }

        $this->connection->insert(
            'category_access_assignment',
            $payload,
            [
                'assignment_id' => ParameterType::STRING,
                'category_id' => ParameterType::STRING,
                'actor_user_id' => ParameterType::STRING,
                'role' => ParameterType::STRING,
                'status' => ParameterType::STRING,
                'is_primary' => ParameterType::INTEGER,
                'granted_at' => ParameterType::STRING,
                'revoked_at' => null === $payload['revoked_at'] ? ParameterType::NULL : ParameterType::STRING,
            ],
        );
    }

    /**
     * Handles the find primary for category id workflow.
     *
     * @throws \Throwable
     */
    public function findPrimaryForCategoryId(string $categoryId): ?CategoryAccessAssignmentInterface
    {
        $assignments = $this->findActiveByCategoryId($categoryId);

        return array_find(
            $assignments,
            static fn (CategoryAccessAssignmentInterface $assignment): bool => $assignment->isPrimary(),
        );
    }

    /**
     * Handles the find active by category id workflow.
     *
     * @throws \Throwable
     */
    /** @noinspection PhpSameParameterValueInspection */
    public function findActiveByCategoryId(string $categoryId): array
    {
        if (!$this->connection instanceof Connection) {
            return array_values(array_filter(
                $this->assignments,
                static fn (CategoryAccessAssignmentInterface $assignment): bool => $assignment->categoryId() === $categoryId
                    && 'active' === $assignment->status(),
            ));
        }

        return $this->hydrateMany(
            $this->connection->fetchAllAssociative(
                'SELECT assignment_id, category_id, actor_user_id, role, status, is_primary, granted_at, revoked_at
                 FROM category_access_assignment
                 WHERE category_id = :categoryId
                   AND status = :status
                 ORDER BY is_primary DESC, granted_at ASC',
                ['categoryId' => $categoryId, 'status' => 'active'],
                ['categoryId' => ParameterType::STRING, 'status' => ParameterType::STRING],
            ),
        );
    }

    /**
     * Handles the find active by actor user id workflow.
     *
     * @throws \Throwable
     */
    public function findActiveByActorUserId(string $actorUserId): array
    {
        if (!$this->connection instanceof Connection) {
            return array_values(array_filter(
                $this->assignments,
                static fn (CategoryAccessAssignmentInterface $assignment): bool => $assignment->actorUserId() === $actorUserId
                    && 'active' === $assignment->status(),
            ));
        }

        return $this->hydrateMany(
            $this->connection->fetchAllAssociative(
                'SELECT assignment_id, category_id, actor_user_id, role, status, is_primary, granted_at, revoked_at
                 FROM category_access_assignment
                 WHERE actor_user_id = :actorUserId
                   AND status = :status
                 ORDER BY granted_at ASC',
                ['actorUserId' => $actorUserId, 'status' => 'active'],
                ['actorUserId' => ParameterType::STRING, 'status' => ParameterType::STRING],
            ),
        );
    }

    /**
     * Handles the find one by category id and actor user id workflow.
     *
     * @throws \Throwable
     */
    public function findOneByCategoryIdAndActorUserId(
        string $categoryId,
        string $actorUserId,
    ): ?CategoryAccessAssignmentInterface {
        if (!$this->connection instanceof Connection) {
            return array_find($this->assignments, fn ($assignment) => $assignment->categoryId() === $categoryId && $assignment->actorUserId() === $actorUserId);
        }

        $row = $this->connection->fetchAssociative(
            'SELECT assignment_id, category_id, actor_user_id, role, status, is_primary, granted_at, revoked_at
             FROM category_access_assignment
             WHERE category_id = :categoryId
               AND actor_user_id = :actorUserId
             LIMIT 1',
            ['categoryId' => $categoryId, 'actorUserId' => $actorUserId],
            ['categoryId' => ParameterType::STRING, 'actorUserId' => ParameterType::STRING],
        );

        return is_array($row) ? $this->hydrateOne($row) : null;
    }

    /**
     * @param list<array<string,mixed>> $rows
     *
     * @return list<CategoryAccessAssignmentInterface>
     *
     * @throws \Throwable
     */
    private function hydrateMany(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->hydrateOne($row);
        }

        return $result;
    }

    /**
     * @param array<string,mixed> $row
     *
     * @throws \Throwable
     */
    private function hydrateOne(array $row): CategoryAccessAssignmentInterface
    {
        return new CategoryAccessAssignment(
            $this->requiredString($row['assignment_id'] ?? null, 'assignment_id'),
            $this->requiredString($row['category_id'] ?? null, 'category_id'),
            $this->requiredString($row['actor_user_id'] ?? null, 'actor_user_id'),
            $this->requiredString($row['role'] ?? null, 'role'),
            $this->requiredString($row['status'] ?? null, 'status'),
            $this->boolValue($row['is_primary'] ?? 0),
            $this->dateTimeImmutable($row['granted_at'] ?? null, 'granted_at'),
            $this->optionalDateTimeImmutable($row['revoked_at'] ?? null),
        );
    }

    private function boolValue(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value) || is_float($value) || is_string($value)) {
            return (bool) $value;
        }

        return false;
    }

    /**
     * @throws \Throwable
     */
    private function dateTimeImmutable(mixed $value, string $field): \DateTimeImmutable
    {
        $normalized = $this->requiredString($value, $field);

        return new \DateTimeImmutable($normalized);
    }

    /**
     * @throws \Throwable
     */
    private function optionalDateTimeImmutable(mixed $value): ?\DateTimeImmutable
    {
        if (null === $value) {
            return null;
        }
        if (!is_scalar($value)) {
            throw new \RuntimeException('Missing scalar value for revoked_at.');
        }

        $normalized = trim((string) $value);
        if ('' === $normalized) {
            return null;
        }

        return new \DateTimeImmutable($normalized);
    }

    /**
     * @throws \Throwable
     */
    private function requiredString(mixed $value, string $field): string
    {
        if (!is_scalar($value)) {
            throw new \RuntimeException(sprintf('Missing scalar value for %s.', $field));
        }

        $normalized = trim((string) $value);
        if ('' === $normalized) {
            throw new \RuntimeException(sprintf('Missing non-empty value for %s.', $field));
        }

        return $normalized;
    }
}
