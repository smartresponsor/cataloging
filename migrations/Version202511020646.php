<?php
declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 */
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202511020646 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create category tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE category (id UUID PRIMARY KEY, slug VARCHAR(255) NOT NULL, parent_id UUID DEFAULT NULL, locale VARCHAR(8) NOT NULL, tenant VARCHAR(64) DEFAULT 'default')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE category');
    }
}
