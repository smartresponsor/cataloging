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
final class Version20251102040100_category_media extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add icon_url to category';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category ADD COLUMN icon_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category DROP COLUMN icon_url');
    }
}
