<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Category attachment schema migration.
 *
 * @noinspection PhpClassNamingConventionInspection
 */
final class Version20251102083000_category_attachment_schema extends AbstractMigration
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    public function getDescription(): string
    {
        return 'Create canonical category_attachment runtime table before additive boundary migrations';
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        if ($schemaManager->tablesExist(['category_attachment'])) {
            return;
        }

        $platform = $this->connection->getDatabasePlatform()->getName();
        $createdAtType = 'postgresql' === $platform ? 'TIMESTAMP(0) WITHOUT TIME ZONE' : 'DATETIME';

        $this->addSql(sprintf(<<<'SQL'
CREATE TABLE category_attachment (
    attachment_id VARCHAR(26) PRIMARY KEY,
    category_id VARCHAR(26) NOT NULL,
    type VARCHAR(64) NOT NULL,
    provider VARCHAR(64) NOT NULL DEFAULT 'attachment',
    external_attachment_id VARCHAR(255) NOT NULL,
    path VARCHAR(2048) NOT NULL,
    created_at %s NOT NULL
)
SQL, $createdAtType));
        $this->addSql(
            'CREATE UNIQUE INDEX ux_category_attachment_external_binding ON category_attachment (category_id, type, provider, external_attachment_id)'
        );
        $this->addSql('CREATE INDEX idx_category_attachment_category ON category_attachment (category_id)');
        $this->addSql('CREATE INDEX idx_category_attachment_provider ON category_attachment (provider)');
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        if ($schemaManager->tablesExist(['category_attachment'])) {
            $this->addSql('DROP TABLE category_attachment');
        }
    }
}
