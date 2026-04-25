<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Category schema migration.
 *
 * @noinspection PhpClassNamingConventionInspection
 */
final class Version20251102030100_category_schema extends AbstractMigration
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    public function getDescription(): string
    {
        return 'Category durable write-model baseline for pgsql + mysql';
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ('postgresql' === $platform) {
            $this->addSql('CREATE EXTENSION IF NOT EXISTS ltree');
            $this->addSql(<<<'SQL'
CREATE TABLE category (
    id VARCHAR(26) PRIMARY KEY,
    name VARCHAR(160) NOT NULL,
    slug VARCHAR(180) NOT NULL,
    parent_id VARCHAR(26) DEFAULT NULL,
    path LTREE NOT NULL,
    depth INT NOT NULL,
    locale VARCHAR(12) DEFAULT NULL,
    tenant VARCHAR(64) NOT NULL DEFAULT 'default',
    workflow_state VARCHAR(32) NOT NULL DEFAULT 'draft',
    published BOOLEAN NOT NULL DEFAULT FALSE,
    published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    icon_url VARCHAR(255) DEFAULT NULL,
    CONSTRAINT uniq_category_slug UNIQUE (slug)
)
SQL);
            $this->addSql('CREATE INDEX idx_category_path ON category USING GIST (path)');
            $this->addSql('CREATE INDEX idx_category_tenant_workflow ON category (tenant, workflow_state)');

            return;
        }

        $this->addSql(<<<'SQL'
CREATE TABLE category (
    id VARCHAR(26) NOT NULL,
    name VARCHAR(160) NOT NULL,
    slug VARCHAR(180) NOT NULL,
    parent_id VARCHAR(26) DEFAULT NULL,
    path VARCHAR(500) NOT NULL,
    depth INT NOT NULL,
    locale VARCHAR(12) DEFAULT NULL,
    tenant VARCHAR(64) NOT NULL DEFAULT 'default',
    workflow_state VARCHAR(32) NOT NULL DEFAULT 'draft',
    published TINYINT(1) NOT NULL DEFAULT 0,
    published_at DATETIME DEFAULT NULL,
    icon_url VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY(id),
    UNIQUE KEY uniq_category_slug (slug),
    KEY idx_category_path (path),
    KEY idx_category_tenant_workflow (tenant, workflow_state)
)
SQL);
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE category');
    }
}
