<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Repository;

use App\RepositoryInterface\CatalogAttachmentRepositoryInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Index;
use Symfony\Component\Uid\Ulid;

/**
 * Provides repository services for catalog attachment repository.
 */
final class CatalogAttachmentRepository implements CatalogAttachmentRepositoryInterface
{
    private bool $schemaEnsured = false;

    /**
     * Initializes the catalog attachment repository service collaborators.
     */
    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * Handles the list workflow.
     */
    public function list(?string $categoryId = null): array
    {
        $this->ensureSchema();

        $sql = '
            SELECT attachment_id, category_id, type, provider, external_attachment_id, path, created_at '
            .'FROM category_attachment';
        $params = [];
        if (null !== $categoryId && '' !== $categoryId) {
            $sql .= ' WHERE category_id = :category_id';
            $params['category_id'] = $categoryId;
        }
        $sql .= ' ORDER BY created_at DESC, attachment_id DESC';

        $rows = $this->connection->fetchAllAssociative($sql, $params);

        return array_values(array_filter(array_map(
            static function (array $row): ?array {
                if (
                    !is_string($row['attachment_id'] ?? null)
                    || !is_string($row['category_id'] ?? null)
                    || !is_string($row['type'] ?? null)
                    || !is_string($row['provider'] ?? null)
                    || !is_string($row['external_attachment_id'] ?? null)
                    || !is_string($row['created_at'] ?? null)
                ) {
                    return null;
                }

                $referenceUri = isset($row['path']) && is_string($row['path']) && '' !== trim($row['path'])
            ? trim($row['path'])
            : null;

                return [
                    'attachment_id' => $row['attachment_id'],
                    'category_id' => $row['category_id'],
                    'type' => $row['type'],
                    'provider' => $row['provider'],
                    'external_attachment_id' => $row['external_attachment_id'],
                    'reference_uri' => $referenceUri,
                    'path' => $referenceUri,
                    'created_at' => $row['created_at'],
                ];
            },
            $rows,
        )));
    }

    /**
     * Handles the add workflow.
     */
    public function add(
        string $categoryId,
        string $type,
        string $provider,
        string $externalAttachmentId,
        ?string $referenceUri = null,
    ): array {
        $this->ensureSchema();

        $existing = $this->connection->fetchAssociative(
            'SELECT attachment_id, category_id, type, provider, external_attachment_id, path, created_at
             FROM category_attachment
             WHERE category_id = :category_id
               AND type = :type
               AND provider = :provider
               AND external_attachment_id = :external_attachment_id
             LIMIT 1',
            [
                'category_id' => $categoryId,
                'type' => $type,
                'provider' => $provider,
                'external_attachment_id' => $externalAttachmentId,
            ],
        );
        if (is_array($existing)) {
            $path = isset($existing['path']) && is_string($existing['path']) && '' !== trim($existing['path'])
                ? trim($existing['path'])
                : null;

            /**
             * @var array{
             *     attachment_id:string,
             *     category_id:string,
             *     type:string,
             *     provider:string,
             *     external_attachment_id:string,
             *     created_at:string,
             * } $existing
             */
            return [
                'attachment_id' => $existing['attachment_id'],
                'category_id' => $existing['category_id'],
                'type' => $existing['type'],
                'provider' => $existing['provider'],
                'external_attachment_id' => $existing['external_attachment_id'],
                'reference_uri' => $path,
                'path' => $path,
                'created_at' => $existing['created_at'],
            ];
        }

        $item = [
            'attachment_id' => (string) new Ulid(),
            'category_id' => $categoryId,
            'type' => $type,
            'provider' => $provider,
            'external_attachment_id' => $externalAttachmentId,
            'path' => null !== $referenceUri ? $referenceUri : '',
            'created_at' => new \DateTimeImmutable()->format(DATE_ATOM),
        ];

        $this->connection->insert('category_attachment', $item);

        return [
            'attachment_id' => $item['attachment_id'],
            'category_id' => $item['category_id'],
            'type' => $item['type'],
            'provider' => $item['provider'],
            'external_attachment_id' => $item['external_attachment_id'],
            'reference_uri' => '' !== $item['path'] ? $item['path'] : null,
            'path' => '' !== $item['path'] ? $item['path'] : null,
            'created_at' => $item['created_at'],
        ];
    }

    /**
     * Handles the find one workflow.
     */
    public function findOne(string $attachmentId): ?array
    {
        $this->ensureSchema();

        $row = $this->connection->fetchAssociative(
            'SELECT attachment_id, category_id, type, provider, external_attachment_id, path, created_at
             FROM category_attachment
             WHERE attachment_id = :attachment_id
             LIMIT 1',
            ['attachment_id' => $attachmentId],
        );
        if (!is_array($row)) {
            return null;
        }
        if (
            !is_string($row['attachment_id'] ?? null)
            || !is_string($row['category_id'] ?? null)
            || !is_string($row['type'] ?? null)
            || !is_string($row['provider'] ?? null)
            || !is_string($row['external_attachment_id'] ?? null)
            || !is_string($row['created_at'] ?? null)
        ) {
            return null;
        }

        $referenceUri = isset($row['path']) && is_string($row['path']) && '' !== trim($row['path'])
            ? trim($row['path'])
            : null;

        return [
            'attachment_id' => $row['attachment_id'],
            'category_id' => $row['category_id'],
            'type' => $row['type'],
            'provider' => $row['provider'],
            'external_attachment_id' => $row['external_attachment_id'],
            'reference_uri' => $referenceUri,
            'path' => $referenceUri,
            'created_at' => $row['created_at'],
        ];
    }

    /**
     * Deletes the requested target from the underlying store.
     */
    public function delete(string $attachmentId): bool
    {
        $this->ensureSchema();

        return 0 < $this->connection->delete('category_attachment', [
            'attachment_id' => $attachmentId,
        ]);
    }

    private function ensureSchema(): void
    {
        if ($this->schemaEnsured) {
            return;
        }

        $schemaManager = $this->connection->createSchemaManager();
        if (!$schemaManager->tablesExist(['category_attachment'])) {
            $this->connection->executeStatement(sprintf(
                'CREATE TABLE category_attachment (
                    attachment_id VARCHAR(26) PRIMARY KEY,
                    category_id VARCHAR(160) NOT NULL,
                    type VARCHAR(64) NOT NULL,
                    provider VARCHAR(64) NOT NULL,
                    external_attachment_id VARCHAR(255) NOT NULL,
                    path VARCHAR(2048) NOT NULL,
                    created_at %s NOT NULL
                )',
                $this->createdAtColumnType(),
            ));
        }

        $columns = [];
        if ($schemaManager->tablesExist(['category_attachment'])) {
            $columns = array_change_key_case($schemaManager->listTableColumns('category_attachment'));
        }

        if (!isset($columns['provider'])) {
            $this->connection->executeStatement(
                "ALTER TABLE category_attachment ADD provider VARCHAR(64) NOT NULL DEFAULT 'attachment'",
            );
        }
        if (!isset($columns['external_attachment_id'])) {
            $this->connection->executeStatement(
                "ALTER TABLE category_attachment ADD external_attachment_id VARCHAR(255) NOT NULL DEFAULT ''",
            );
            $this->connection->executeStatement(
                "UPDATE category_attachment SET external_attachment_id = path WHERE external_attachment_id = ''",
            );
        }

        $indexes = [];
        if ($schemaManager->tablesExist(['category_attachment'])) {
            foreach ($schemaManager->listTableIndexes('category_attachment') as $index) {
                if ($index instanceof Index) {
                    $indexes[strtolower($index->getName())] = true;
                }
            }
        }

        if (!isset($indexes['ux_category_attachment_external_binding'])) {
            $this->connection->executeStatement(
                'CREATE UNIQUE INDEX ux_category_attachment_external_binding
                 ON category_attachment (category_id, type, provider, external_attachment_id)',
            );
        }
        if (!isset($indexes['idx_category_attachment_category'])) {
            $this->connection->executeStatement(
                'CREATE INDEX idx_category_attachment_category
                 ON category_attachment (category_id)',
            );
        }
        if (!isset($indexes['idx_category_attachment_provider'])) {
            $this->connection->executeStatement(
                'CREATE INDEX idx_category_attachment_provider
                 ON category_attachment (provider)',
            );
        }

        $this->schemaEnsured = true;
    }

    private function createdAtColumnType(): string
    {
        $platformClass = $this->connection->getDatabasePlatform()::class;

        return match (true) {
            str_contains($platformClass, 'Postgre') => 'TIMESTAMPTZ',
            str_contains($platformClass, 'MySQL') => 'DATETIME',
            str_contains($platformClass, 'SQLite') => 'TEXT',
            default => 'TIMESTAMP',
        };
    }
}
