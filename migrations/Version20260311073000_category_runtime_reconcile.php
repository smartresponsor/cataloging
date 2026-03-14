<?php
declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260311073000_category_runtime_reconcile extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Reconcile runtime schema with fixture/demo and current category entities';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE category ADD COLUMN IF NOT EXISTS icon_url VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE category ADD COLUMN IF NOT EXISTS depth INT NOT NULL DEFAULT 0");
        $this->addSql("CREATE TABLE IF NOT EXISTS category_alias (id SERIAL PRIMARY KEY, old_slug VARCHAR(180) NOT NULL, category_id VARCHAR(26) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL)");
        $this->addSql("CREATE TABLE IF NOT EXISTS category_banner (id SERIAL PRIMARY KEY, category_id VARCHAR(26) NOT NULL, title VARCHAR(160) NOT NULL, content TEXT NOT NULL, is_draft BOOLEAN NOT NULL DEFAULT TRUE, published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL)");
        $this->addSql("CREATE TABLE IF NOT EXISTS category_html_block (id SERIAL PRIMARY KEY, category_id VARCHAR(26) NOT NULL, html TEXT NOT NULL, is_draft BOOLEAN NOT NULL DEFAULT TRUE)");
        $this->addSql("CREATE TABLE IF NOT EXISTS category_pin (id SERIAL PRIMARY KEY, category_id VARCHAR(26) NOT NULL, record_id VARCHAR(64) NOT NULL, position INT NOT NULL DEFAULT 0, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL)");
        $this->addSql("CREATE UNIQUE INDEX IF NOT EXISTS uniq_category_pin ON category_pin (category_id, record_id)");
        $this->addSql("CREATE INDEX IF NOT EXISTS idx_category_slug ON category (slug)");
        $this->addSql("CREATE INDEX IF NOT EXISTS idx_category_path ON category (path)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP INDEX IF EXISTS idx_category_path");
        $this->addSql("DROP INDEX IF EXISTS idx_category_slug");
        $this->addSql("DROP INDEX IF EXISTS uniq_category_pin");
        $this->addSql("DROP TABLE IF EXISTS category_pin");
        $this->addSql("DROP TABLE IF EXISTS category_html_block");
        $this->addSql("DROP TABLE IF EXISTS category_banner");
        $this->addSql("DROP TABLE IF EXISTS category_alias");
    }
}
