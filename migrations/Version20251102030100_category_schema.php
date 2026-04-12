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
        return 'Category schema for pgsql + mysql';
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
CREATE TABLE category (
    id UUID PRIMARY KEY,
    slug VARCHAR(190) NOT NULL,
    name VARCHAR(190) NOT NULL,
    parent_id UUID DEFAULT NULL,
    level INT NOT NULL DEFAULT 0,
    path VARCHAR(500) DEFAULT NULL,
    locale VARCHAR(12) DEFAULT NULL
)
SQL);
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE category');
    }
}
