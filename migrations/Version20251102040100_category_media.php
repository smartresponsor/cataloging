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
        return 'Add icon_url to category when baseline schema does not already contain it';
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function up(Schema $schema): void
    {
        if ($this->hasColumn('category', 'icon_url')) {
            return;
        }

        $this->addSql('ALTER TABLE category ADD COLUMN icon_url VARCHAR(255) DEFAULT NULL');
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function down(Schema $schema): void
    {
        if (!$this->hasColumn('category', 'icon_url')) {
            return;
        }

        $this->addSql('ALTER TABLE category DROP COLUMN icon_url');
    }

    private function hasColumn(string $table, string $column): bool
    {
        return $this->connection->createSchemaManager()->introspectTable($table)->hasColumn($column);
    }
}
