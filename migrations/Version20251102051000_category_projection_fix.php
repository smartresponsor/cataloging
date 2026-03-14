<?php
declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251102051000_category_projection_fix extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Align MySQL and Postgres category projection tables';
    }
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category_projection_mysql ADD COLUMN channel VARCHAR(32) DEFAULT "default"');
    }
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category_projection_mysql DROP COLUMN channel');
    }
}
