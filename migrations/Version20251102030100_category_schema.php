<?php
declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251102030100_category_schema extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'tests schema for pgsql + mysql';
    }
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE category (id UUID PRIMARY KEY, slug VARCHAR(190) NOT NULL, name VARCHAR(190) NOT NULL, parent_id UUID DEFAULT NULL, level INT NOT NULL DEFAULT 0, path VARCHAR(500) DEFAULT NULL, locale VARCHAR(12) DEFAULT NULL)');
    }
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE category');
    }
}
