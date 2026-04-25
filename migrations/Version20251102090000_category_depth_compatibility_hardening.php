<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Category depth compatibility hardening migration.
 *
 * @noinspection PhpClassNamingConventionInspection
 */
final class Version20251102090000_category_depth_compatibility_hardening extends AbstractMigration
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    public function getDescription(): string
    {
        return 'Ensure canonical category.depth exists and is populated from legacy category.level when needed';
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        if (!$schemaManager->tablesExist(['category'])) {
            return;
        }

        $columns = array_change_key_case($schemaManager->listTableColumns('category'));
        if (isset($columns['depth'])) {
            return;
        }

        if (!isset($columns['level'])) {
            $this->abortIf(true, 'Category table has neither canonical depth nor legacy level column.');
        }

        $platform = $this->connection->getDatabasePlatform()->getName();
        if ('postgresql' === $platform) {
            $this->addSql('ALTER TABLE category ADD COLUMN depth INT DEFAULT 0');
            $this->addSql('UPDATE category SET depth = level WHERE depth = 0');
            $this->addSql('ALTER TABLE category ALTER COLUMN depth SET NOT NULL');

            return;
        }

        $this->addSql('ALTER TABLE category ADD depth INT NOT NULL DEFAULT 0');
        $this->addSql('UPDATE category SET depth = level WHERE depth = 0');
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        if (!$schemaManager->tablesExist(['category'])) {
            return;
        }

        $columns = array_change_key_case($schemaManager->listTableColumns('category'));
        if (isset($columns['depth'])) {
            $this->addSql('ALTER TABLE category DROP COLUMN depth');
        }
    }
}
