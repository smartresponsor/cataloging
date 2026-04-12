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
        return 'Align MySQL and Postgres category projection tables';
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function up(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE category_projection_mysql ADD COLUMN channel VARCHAR(32) DEFAULT "default"'
        );
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category_projection_mysql DROP COLUMN channel');
    }
}
