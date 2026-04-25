<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251102095000_catalog_projection_doctrine_first_baseline extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Introduce Doctrine-first baseline for category_projection and virtual_category_member read/runtime entities';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['category_projection'])) {
            $this->addSql("CREATE TABLE category_projection (id VARCHAR(26) NOT NULL, slug VARCHAR(180) NOT NULL, name VARCHAR(160) NOT NULL, parent_id VARCHAR(26) DEFAULT NULL, path VARCHAR(255) NOT NULL, locale VARCHAR(12) DEFAULT NULL, tenant VARCHAR(64) NOT NULL DEFAULT 'default', workflow_state VARCHAR(32) NOT NULL DEFAULT 'draft', published BOOLEAN NOT NULL DEFAULT FALSE, published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))");
            $this->addSql('CREATE INDEX idx_category_projection_path ON category_projection (path)');
            $this->addSql('CREATE INDEX idx_category_projection_tenant_locale ON category_projection (tenant, locale)');
            $this->addSql('CREATE INDEX idx_category_projection_workflow_state ON category_projection (workflow_state)');
            $this->addSql('CREATE INDEX idx_category_projection_updated_at ON category_projection (updated_at)');
        }

        if (!$schemaManager->tablesExist(['virtual_category_member'])) {
            $this->addSql('CREATE TABLE virtual_category_member (id SERIAL NOT NULL, virtual_category_id VARCHAR(26) NOT NULL, record_id VARCHAR(64) NOT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX idx_virtual_category_member_record ON virtual_category_member (record_id)');
        }
    }

    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        if ($schemaManager->tablesExist(['virtual_category_member'])) {
            $this->addSql('DROP TABLE virtual_category_member');
        }

        if ($schemaManager->tablesExist(['category_projection'])) {
            $this->addSql('DROP TABLE category_projection');
        }
    }
}
