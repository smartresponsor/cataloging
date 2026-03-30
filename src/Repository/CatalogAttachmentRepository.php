<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Repository;

use App\RepositoryInterface\CatalogAttachmentRepositoryInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Index;
use Symfony\Component\Uid\Ulid;

final class CatalogAttachmentRepository implements CatalogAttachmentRepositoryInterface
{
    private bool $schemaEnsured = false;

    public function __construct(private readonly Connection $connection)
    {
    }

    public function list(?string $categoryId = null): array
    {
        $this->ensureSchema();

        $sql = 'SELECT attachment_id, category_id, type, path, created_at FROM category_attachment';
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
                    || !is_string($row['path'] ?? null)
                    || !is_string($row['created_at'] ?? null)
                ) {
                    return null;
                }

                return [
                    'attachment_id' => $row['attachment_id'],
                    'category_id' => $row['category_id'],
                    'type' => $row['type'],
                    'path' => $row['path'],
                    'created_at' => $row['created_at'],
                ];
            },
            $rows,
        )));
    }

    public function add(string $categoryId, string $type, string $path): array
    {
        $this->ensureSchema();

        $existing = $this->connection->fetchAssociative(
            'SELECT attachment_id, category_id, type, path, created_at
             FROM category_attachment
             WHERE category_id = :category_id AND type = :type AND path = :path
             LIMIT 1',
            [
                'category_id' => $categoryId,
                'type' => $type,
                'path' => $path,
            ],
        );
        if (is_array($existing)) {
            /** @var array{attachment_id:string,category_id:string,type:string,path:string,created_at:string} $existing */
            return $existing;
        }

        $item = [
            'attachment_id' => (string) new Ulid(),
            'category_id' => $categoryId,
            'type' => $type,
            'path' => $path,
            'created_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
        ];

        $this->connection->insert('category_attachment', $item);

        return $item;
    }

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
                    path VARCHAR(2048) NOT NULL,
                    created_at %s NOT NULL
                )',
                $this->createdAtColumnType(),
            ));
        }

        $indexes = [];
        if ($schemaManager->tablesExist(['category_attachment'])) {
            foreach ($schemaManager->listTableIndexes('category_attachment') as $index) {
                if ($index instanceof Index) {
                    $indexes[strtolower($index->getName())] = true;
                }
            }
        }

        if (!isset($indexes['ux_category_attachment_identity'])) {
            $this->connection->executeStatement(
                'CREATE UNIQUE INDEX ux_category_attachment_identity
                 ON category_attachment (category_id, type, path)',
            );
        }
        if (!isset($indexes['idx_category_attachment_category'])) {
            $this->connection->executeStatement(
                'CREATE INDEX idx_category_attachment_category
                 ON category_attachment (category_id)',
            );
        }

        $this->schemaEnsured = true;
    }

    private function createdAtColumnType(): string
    {
        $platformName = $this->connection->getDatabasePlatform()->getName();

        return match (true) {
            str_contains($platformName, 'postgres') => 'TIMESTAMPTZ',
            str_contains($platformName, 'mysql') => 'DATETIME',
            str_contains($platformName, 'sqlite') => 'TEXT',
            default => 'TIMESTAMP',
        };
    }
}
