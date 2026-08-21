<?php

declare(strict_types=1);

namespace App\Cataloging\Migrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260820090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add category classification metadata using an additive production-safe migration.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform,
            'Cataloging category metadata migration supports PostgreSQL only.',
        );
        $this->abortIf(!$this->tableExists('category'), 'Cataloging category table is required before adding metadata.');

        if (!$this->columnExists('category', 'metadata')) {
            $this->addSql("ALTER TABLE category ADD metadata JSON NOT NULL DEFAULT '{}'::json");
            return;
        }

        $type = (string) $this->connection->fetchOne(
            "SELECT data_type FROM information_schema.columns WHERE table_schema = 'public' AND table_name = 'category' AND column_name = 'metadata'",
        );
        $this->abortIf(!in_array($type, ['json', 'jsonb'], true), 'Existing category.metadata must use PostgreSQL json or jsonb.');
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Category metadata is durable classification data and is not removed automatically.');
    }

    private function tableExists(string $table): bool
    {
        return 1 === (int) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public' AND table_name = ?",
            [$table],
        );
    }

    private function columnExists(string $table, string $column): bool
    {
        return 1 === (int) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = 'public' AND table_name = ? AND column_name = ?",
            [$table, $column],
        );
    }
}
