<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Category attachment external boundary migration.
 *
 * @noinspection PhpClassNamingConventionInspection
 */
final class Version20251102084000_category_attachment_external_boundary extends AbstractMigration
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    public function getDescription(): string
    {
        return 'Harden category attachment binding as external-reference boundary';
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        if (!$schemaManager->tablesExist(['category_attachment'])) {
            return;
        }

        $columns = array_change_key_case($schemaManager->listTableColumns('category_attachment'));
        if (!isset($columns['provider'])) {
            $this->addSql(
                "ALTER TABLE category_attachment ADD provider VARCHAR(64) NOT NULL DEFAULT 'attachment'"
            );
        }
        if (!isset($columns['external_attachment_id'])) {
            $this->addSql(
                "ALTER TABLE category_attachment ADD external_attachment_id VARCHAR(255) NOT NULL DEFAULT ''"
            );
            $this->addSql(
                "UPDATE category_attachment SET external_attachment_id = path WHERE external_attachment_id = ''"
            );
        }

        $indexes = [];
        foreach ($schemaManager->introspectTableIndexesByUnquotedName('category_attachment') as $indexName => $index) {
            $indexes[strtolower((string) $indexName)] = true;
        }

        if (isset($indexes['ux_category_attachment_identity'])) {
            $this->addSql('DROP INDEX ux_category_attachment_identity');
        }
        if (!isset($indexes['ux_category_attachment_external_binding'])) {
            $this->addSql(
                'CREATE UNIQUE INDEX ux_category_attachment_external_binding ON category_attachment (category_id, type, provider, external_attachment_id)'
            );
        }
        if (!isset($indexes['idx_category_attachment_provider'])) {
            $this->addSql('CREATE INDEX idx_category_attachment_provider ON category_attachment (provider)');
        }
        if (!isset($indexes['idx_category_attachment_category'])) {
            $this->addSql('CREATE INDEX idx_category_attachment_category ON category_attachment (category_id)');
        }
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        if (!$schemaManager->tablesExist(['category_attachment'])) {
            return;
        }

        $indexes = [];
        foreach ($schemaManager->introspectTableIndexesByUnquotedName('category_attachment') as $indexName => $index) {
            $indexes[strtolower((string) $indexName)] = true;
        }

        if (isset($indexes['idx_category_attachment_provider'])) {
            $this->addSql('DROP INDEX idx_category_attachment_provider');
        }
        if (isset($indexes['ux_category_attachment_external_binding'])) {
            $this->addSql('DROP INDEX ux_category_attachment_external_binding');
        }
        if (!isset($indexes['ux_category_attachment_identity']) && isset($schemaManager->listTableColumns('category_attachment')['path'])) {
            $this->addSql(
                'CREATE UNIQUE INDEX ux_category_attachment_identity ON category_attachment (category_id, type, path)'
            );
        }

        $columns = array_change_key_case($schemaManager->listTableColumns('category_attachment'));
        if (isset($columns['external_attachment_id'])) {
            $this->addSql('ALTER TABLE category_attachment DROP COLUMN external_attachment_id');
        }
        if (isset($columns['provider'])) {
            $this->addSql('ALTER TABLE category_attachment DROP COLUMN provider');
        }
    }
}
