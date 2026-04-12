<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Category media migration.
 *
 * @noinspection PhpClassNamingConventionInspection
 */
final class Version20251102040100_category_media extends AbstractMigration
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    public function getDescription(): string
    {
        return 'Add icon_url to category';
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category ADD COLUMN icon_url VARCHAR(255) DEFAULT NULL');
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category DROP COLUMN icon_url');
    }
}
