<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Category runtime write baseline migration.
 *
 * @noinspection PhpClassNamingConventionInspection
 */
final class Version20251102072000_category_runtime_write_baseline extends AbstractMigration
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    public function getDescription(): string
    {
        return 'Add category publication state and outbox baseline for runtime write flows when missing from baseline schema';
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function up(Schema $schema): void
    {
        $publishedType = 'postgresql' === $this->connection->getDatabasePlatform()->getName()
            ? 'TIMESTAMP(0) WITHOUT TIME ZONE'
            : 'DATETIME';

        if (!$this->hasColumn('category', 'workflow_state')) {
            $this->addSql("ALTER TABLE category ADD COLUMN workflow_state VARCHAR(32) NOT NULL DEFAULT 'draft'");
        }

        if (!$this->hasColumn('category', 'published')) {
            $this->addSql('ALTER TABLE category ADD COLUMN published BOOLEAN NOT NULL DEFAULT FALSE');
        }

        if (!$this->hasColumn('category', 'published_at')) {
            $this->addSql(sprintf('ALTER TABLE category ADD COLUMN published_at %s DEFAULT NULL', $publishedType));
        }
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function down(Schema $schema): void
    {
        if ($this->hasColumn('category', 'published_at')) {
            $this->addSql('ALTER TABLE category DROP COLUMN published_at');
        }

        if ($this->hasColumn('category', 'published')) {
            $this->addSql('ALTER TABLE category DROP COLUMN published');
        }

        if ($this->hasColumn('category', 'workflow_state')) {
            $this->addSql('ALTER TABLE category DROP COLUMN workflow_state');
        }
    }

    private function hasColumn(string $table, string $column): bool
    {
        return $this->connection->createSchemaManager()->introspectTable($table)->hasColumn($column);
    }
}
