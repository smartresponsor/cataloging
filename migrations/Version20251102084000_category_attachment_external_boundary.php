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
        $this->addSql(
            "ALTER TABLE category_attachment ADD provider VARCHAR(64) NOT NULL DEFAULT 'attachment'"
        );
        $this->addSql(
            "ALTER TABLE category_attachment ADD external_attachment_id VARCHAR(255) NOT NULL DEFAULT ''"
        );
        $this->addSql(
            "UPDATE category_attachment SET external_attachment_id = path WHERE external_attachment_id = ''"
        );
        $this->addSql('DROP INDEX IF EXISTS ux_category_attachment_identity');
        $this->addSql(
            'CREATE UNIQUE INDEX ux_category_attachment_external_binding ON category_attachment (category_id, type, provider, external_attachment_id)'
        );
        $this->addSql('CREATE INDEX idx_category_attachment_provider ON category_attachment (provider)');
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IF EXISTS idx_category_attachment_provider');
        $this->addSql('DROP INDEX IF EXISTS ux_category_attachment_external_binding');
        $this->addSql(
            'CREATE UNIQUE INDEX ux_category_attachment_identity ON category_attachment (category_id, type, path)'
        );
        $this->addSql('ALTER TABLE category_attachment DROP COLUMN external_attachment_id');
        $this->addSql('ALTER TABLE category_attachment DROP COLUMN provider');
    }
}
