<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251102094000_catalog_governance_doctrine_first_baseline extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Introduces Doctrine-first baseline tables for catalog governance records and media bindings.';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['category_change_request'])) {
            $this->addSql("CREATE TABLE category_change_request (request_id VARCHAR(64) NOT NULL, category_id VARCHAR(26) NOT NULL, submitted_by VARCHAR(190) NOT NULL, summary VARCHAR(500) NOT NULL, changes JSON NOT NULL, state VARCHAR(32) NOT NULL, reviewed_by VARCHAR(190) DEFAULT NULL, decision_reason LONGTEXT DEFAULT NULL, submitted_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', reviewed_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(request_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
            $this->addSql('CREATE INDEX idx_category_change_request_category_state ON category_change_request (category_id, state)');
        }

        if (!$schemaManager->tablesExist(['category_review_assignment'])) {
            $this->addSql("CREATE TABLE category_review_assignment (request_id VARCHAR(64) NOT NULL, category_id VARCHAR(26) NOT NULL, assigned_reviewer VARCHAR(190) NOT NULL, assigned_by VARCHAR(190) NOT NULL, priority VARCHAR(32) NOT NULL, assigned_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', due_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(request_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
            $this->addSql('CREATE INDEX idx_category_review_assignment_reviewer ON category_review_assignment (assigned_reviewer)');
            $this->addSql('CREATE INDEX idx_category_review_assignment_category ON category_review_assignment (category_id)');
        }

        if (!$schemaManager->tablesExist(['category_workflow'])) {
            $this->addSql("CREATE TABLE category_workflow (category_id VARCHAR(26) NOT NULL, state_value VARCHAR(32) NOT NULL, actor_id VARCHAR(190) NOT NULL, reason LONGTEXT NOT NULL, transitioned_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        }

        if (!$schemaManager->tablesExist(['category_media_binding'])) {
            $this->addSql("CREATE TABLE category_media_binding (binding_id VARCHAR(64) NOT NULL, category_id VARCHAR(26) NOT NULL, asset_id VARCHAR(190) NOT NULL, role_name VARCHAR(32) NOT NULL, channels JSON NOT NULL, locales JSON NOT NULL, required_for_publish TINYINT(1) NOT NULL, active TINYINT(1) NOT NULL, metadata JSON NOT NULL, actor_id VARCHAR(190) NOT NULL, bound_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(binding_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
            $this->addSql('CREATE INDEX idx_category_media_binding_category_active ON category_media_binding (category_id, active)');
        }

        if (!$schemaManager->tablesExist(['virtual_category'])) {
            $this->addSql("CREATE TABLE virtual_category (id VARCHAR(26) NOT NULL, name VARCHAR(160) NOT NULL, rule JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        }
    }

    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        foreach (['category_media_binding', 'category_workflow', 'category_review_assignment', 'category_change_request', 'virtual_category'] as $table) {
            if ($schemaManager->tablesExist([$table])) {
                $this->addSql('DROP TABLE '.$table);
            }
        }
    }
}
