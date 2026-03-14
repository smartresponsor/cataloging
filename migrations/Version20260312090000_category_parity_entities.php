<?php
declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260312090000_category_parity_entities extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add parity tables for CategoryTaxonomy, CategoryLink, CategoryRedirect, ProjectionControlEntity, and VirtualCategoryEntity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE IF NOT EXISTS category_taxonomy (
            id VARCHAR(26) PRIMARY KEY,
            code VARCHAR(64) NOT NULL,
            name JSON NOT NULL,
            rule JSON NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        )");
        $this->addSql("CREATE UNIQUE INDEX IF NOT EXISTS uniq_category_taxonomy_code ON category_taxonomy (code)");

        $this->addSql("CREATE TABLE IF NOT EXISTS category_link (
            id VARCHAR(26) PRIMARY KEY,
            taxonomy_id VARCHAR(26) NOT NULL,
            category_id VARCHAR(26) NOT NULL,
            target_domain VARCHAR(64) NOT NULL,
            target_class VARCHAR(128) NOT NULL,
            target_id VARCHAR(64) NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        )");
        $this->addSql("CREATE UNIQUE INDEX IF NOT EXISTS uniq_category_link_target ON category_link (category_id, target_domain, target_class, target_id)");

        $this->addSql("CREATE TABLE IF NOT EXISTS category_redirect (
            id VARCHAR(26) PRIMARY KEY,
            from_path VARCHAR(255) NOT NULL,
            to_path VARCHAR(255) NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        )");
        $this->addSql("CREATE UNIQUE INDEX IF NOT EXISTS uniq_category_redirect_from ON category_redirect (from_path)");

        $this->addSql("CREATE TABLE IF NOT EXISTS projection_control (
            id VARCHAR(32) PRIMARY KEY,
            paused BOOLEAN NOT NULL DEFAULT FALSE
        )");
        $this->addSql("INSERT INTO projection_control (id, paused)
            SELECT 'category', FALSE
            WHERE NOT EXISTS (SELECT 1 FROM projection_control WHERE id = 'category')");

        $this->addSql("CREATE TABLE IF NOT EXISTS virtual_category (
            id VARCHAR(26) PRIMARY KEY,
            name VARCHAR(160) NOT NULL,
            rule JSON NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        )");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE IF EXISTS virtual_category");
        $this->addSql("DROP TABLE IF EXISTS projection_control");
        $this->addSql("DROP INDEX IF EXISTS uniq_category_redirect_from");
        $this->addSql("DROP TABLE IF EXISTS category_redirect");
        $this->addSql("DROP INDEX IF EXISTS uniq_category_link_target");
        $this->addSql("DROP TABLE IF EXISTS category_link");
        $this->addSql("DROP INDEX IF EXISTS uniq_category_taxonomy_code");
        $this->addSql("DROP TABLE IF EXISTS category_taxonomy");
    }
}
