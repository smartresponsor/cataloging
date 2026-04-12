<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Projection;

use App\ProjectionInterface\CategoryProjectionSyncInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Projection sync worker that updates MySQL read models from outbox events.
 */
final readonly class CategoryProjectionSync implements CategoryProjectionSyncInterface
{
    /**
     * Initializes the category projection sync service collaborators.
     */
    public function __construct(private ManagerRegistry $registry)
    {
    }

    /**
     * @param array<string,mixed> $event
     *
     * @throws Exception
     */
    public function apply(array $event): void
    {
        $type = $this->stringValue($event['type'] ?? null);
        if (!in_array($type, ['category.moved', 'category.published', 'category.unpublished'], true)) {
            return;
        }

        $payload = $event['payload'] ?? null;
        $categoryId = is_array($payload) ? $this->stringValue($payload['categoryId'] ?? null) : '';
        if ('' === $categoryId) {
            throw new \InvalidArgumentException('Projection event is missing categoryId.');
        }

        $row = $this->dataConnection()->fetchAssociative(
            'SELECT id, slug, name, parent_id, path, locale, tenant, workflow_state, published, published_at
             FROM category
             WHERE id = :id
             LIMIT 1',
            ['id' => $categoryId],
            ['id' => ParameterType::STRING],
        );

        if (!is_array($row)) {
            throw new \RuntimeException(sprintf('Projection source category "%s" was not found.', $categoryId));
        }

        $parentId = $this->nullableStringValue($row['parent_id'] ?? null);
        $publishedAt = $this->nullableStringValue($row['published_at'] ?? null);

        $sql = 'INSERT INTO category_projection (id, slug, name, parent_id, path, locale, tenant, '
            .'workflow_state, published, published_at, updated_at) '
            .'VALUES (:id, :slug, :name, :parentId, :path, :locale, :tenant, :workflowState, '
            .':published, :publishedAt, :updatedAt) '
            .'ON DUPLICATE KEY UPDATE slug = VALUES(slug), name = VALUES(name), '
            .'parent_id = VALUES(parent_id), path = VALUES(path), locale = VALUES(locale), '
            .'tenant = VALUES(tenant), workflow_state = VALUES(workflow_state), '
            .'published = VALUES(published), published_at = VALUES(published_at), '
            .'updated_at = VALUES(updated_at)';

        $this->infraConnection()->executeStatement(
            $sql,
            [
                'id' => $this->stringValue($row['id'] ?? null),
                'slug' => $this->stringValue($row['slug'] ?? null),
                'name' => $this->stringValue($row['name'] ?? null),
                'parentId' => $parentId,
                'path' => $this->stringValue($row['path'] ?? null),
                'locale' => $this->stringValue($row['locale'] ?? null),
                'tenant' => $this->stringValue($row['tenant'] ?? 'default'),
                'workflowState' => $this->stringValue($row['workflow_state'] ?? 'draft'),
                'published' => $this->boolValue($row['published'] ?? false),
                'publishedAt' => $publishedAt,
                'updatedAt' => new \DateTimeImmutable('now')->format('Y-m-d H:i:s'),
            ],
            [
                'id' => ParameterType::STRING,
                'slug' => ParameterType::STRING,
                'name' => ParameterType::STRING,
                'parentId' => ParameterType::STRING,
                'path' => ParameterType::STRING,
                'locale' => ParameterType::STRING,
                'tenant' => ParameterType::STRING,
                'workflowState' => ParameterType::STRING,
                'published' => ParameterType::BOOLEAN,
                'publishedAt' => ParameterType::STRING,
                'updatedAt' => ParameterType::STRING,
            ],
        );
    }

    private function dataConnection(): Connection
    {
        /** @var Connection $connection */
        $connection = $this->registry->getConnection('data');

        return $connection;
    }

    private function infraConnection(): Connection
    {
        /** @var Connection $connection */
        $connection = $this->registry->getConnection('infra');

        return $connection;
    }

    private function stringValue(mixed $value): string
    {
        if (!is_scalar($value)) {
            return '';
        }

        return trim((string) $value);
    }

    private function nullableStringValue(mixed $value): ?string
    {
        $normalized = $this->stringValue($value);

        return '' === $normalized ? null : $normalized;
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
