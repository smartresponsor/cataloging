<?php
declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 */
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251102010100_category_audit extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create category_audit table';
    }
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE category_audit (id UUID PRIMARY KEY, action VARCHAR(64) NOT NULL, payload JSONB NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL)');
    }
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE category_audit');
    }
}
