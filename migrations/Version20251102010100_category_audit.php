<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Category audit migration.
 *
 * @noinspection PhpClassNamingConventionInspection
 */
final class Version20251102010100_category_audit extends AbstractMigration
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    public function getDescription(): string
    {
        return 'Create category_audit table';
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
CREATE TABLE category_audit (
    id UUID PRIMARY KEY,
    action VARCHAR(64) NOT NULL,
    payload JSONB NOT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
)
SQL);
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE category_audit');
    }
}
