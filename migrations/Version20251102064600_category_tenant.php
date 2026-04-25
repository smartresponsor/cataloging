<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Category tenant migration.
 *
 * @noinspection PhpClassNamingConventionInspection
 */
final class Version20251102064600_category_tenant extends AbstractMigration
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    public function getDescription(): string
    {
        return 'Add tenant column to category when baseline schema does not already contain it';
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function up(Schema $schema): void
    {
        if ($this->hasColumn('category', 'tenant')) {
            return;
        }

        $this->addSql("ALTER TABLE category ADD COLUMN tenant VARCHAR(64) DEFAULT 'default'");
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function down(Schema $schema): void
    {
        if (!$this->hasColumn('category', 'tenant')) {
            return;
        }

        $this->addSql('ALTER TABLE category DROP COLUMN tenant');
    }

    private function hasColumn(string $table, string $column): bool
    {
        return $this->connection->createSchemaManager()->introspectTable($table)->hasColumn($column);
    }
}
