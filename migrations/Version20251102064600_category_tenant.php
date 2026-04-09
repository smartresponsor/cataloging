<?php
declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 */
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/** @noinspection PhpMissingParentCallCommonInspection */
final class Version20251102064600_category_tenant extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add tenant column to category table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE category ADD COLUMN tenant VARCHAR(64) DEFAULT 'default'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category DROP COLUMN tenant');
    }
}
