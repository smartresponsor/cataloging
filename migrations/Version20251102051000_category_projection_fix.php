<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Category projection alignment migration.
 *
 * @noinspection PhpClassNamingConventionInspection
 */
final class Version20251102051000_category_projection_fix extends AbstractMigration
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    public function getDescription(): string
    {
        return 'Retire legacy category_projection_mysql drift and align canonical category_projection table';
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        if ($schemaManager->tablesExist(['category_projection_mysql'])) {
            $legacyColumns = array_change_key_case($schemaManager->listTableColumns('category_projection_mysql'));
            if (!isset($legacyColumns['channel'])) {
                $this->addSql("ALTER TABLE category_projection_mysql ADD COLUMN channel VARCHAR(32) DEFAULT 'default'");
            }

            return;
        }

        if (!$schemaManager->tablesExist(['category_projection'])) {
            return;
        }

        $columns = array_change_key_case($schemaManager->listTableColumns('category_projection'));
        if (!isset($columns['tenant'])) {
            $this->addSql("ALTER TABLE category_projection ADD COLUMN tenant VARCHAR(64) NOT NULL DEFAULT 'default'");
        }
        if (!isset($columns['workflow_state'])) {
            $this->addSql("ALTER TABLE category_projection ADD COLUMN workflow_state VARCHAR(32) NOT NULL DEFAULT 'draft'");
        }
        if (!isset($columns['published'])) {
            $platform = $this->connection->getDatabasePlatform()->getName();
            $publishedType = 'postgresql' === $platform ? 'BOOLEAN' : 'TINYINT(1)';
            $publishedDefault = 'postgresql' === $platform ? 'FALSE' : '0';
            $this->addSql(
                sprintf(
                    'ALTER TABLE category_projection ADD COLUMN published %s NOT NULL DEFAULT %s',
                    $publishedType,
                    $publishedDefault,
                )
            );
        }
        if (!isset($columns['published_at'])) {
            $publishedAtType = 'postgresql' === $this->connection->getDatabasePlatform()->getName()
                ? 'TIMESTAMP(0) WITHOUT TIME ZONE'
                : 'DATETIME';
            $this->addSql(
                sprintf(
                    'ALTER TABLE category_projection ADD COLUMN published_at %s DEFAULT NULL',
                    $publishedAtType,
                )
            );
        }
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        if ($schemaManager->tablesExist(['category_projection_mysql'])) {
            $legacyColumns = array_change_key_case($schemaManager->listTableColumns('category_projection_mysql'));
            if (isset($legacyColumns['channel'])) {
                $this->addSql('ALTER TABLE category_projection_mysql DROP COLUMN channel');
            }

            return;
        }

        if (!$schemaManager->tablesExist(['category_projection'])) {
            return;
        }

        $columns = array_change_key_case($schemaManager->listTableColumns('category_projection'));
        if (isset($columns['published_at'])) {
            $this->addSql('ALTER TABLE category_projection DROP COLUMN published_at');
        }
        if (isset($columns['published'])) {
            $this->addSql('ALTER TABLE category_projection DROP COLUMN published');
        }
        if (isset($columns['workflow_state'])) {
            $this->addSql('ALTER TABLE category_projection DROP COLUMN workflow_state');
        }
        if (isset($columns['tenant'])) {
            $this->addSql('ALTER TABLE category_projection DROP COLUMN tenant');
        }
    }
}
