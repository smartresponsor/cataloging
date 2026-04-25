<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251102097000_catalog_redirect_store_doctrine_first_baseline extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Introduce Doctrine-first baseline for seo_redirect durable redirect storage';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['seo_redirect'])) {
            $this->addSql('CREATE TABLE seo_redirect (from_path VARCHAR(255) NOT NULL, to_path VARCHAR(255) NOT NULL, status INT NOT NULL, PRIMARY KEY(from_path))');
        }
    }

    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        if ($schemaManager->tablesExist(['seo_redirect'])) {
            $this->addSql('DROP TABLE seo_redirect');
        }
    }
}
