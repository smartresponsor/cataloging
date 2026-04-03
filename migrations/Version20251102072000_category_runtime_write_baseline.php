<?php
declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 */
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251102072000_category_runtime_write_baseline extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add category publication state and outbox baseline for runtime write flows';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE category ADD COLUMN workflow_state VARCHAR(32) NOT NULL DEFAULT 'draft'");
        $this->addSql('ALTER TABLE category ADD COLUMN published BOOLEAN NOT NULL DEFAULT FALSE');
        $this->addSql('ALTER TABLE category ADD COLUMN published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('CREATE TABLE outbox (id UUID PRIMARY KEY, type VARCHAR(190) NOT NULL, payload JSONB NOT NULL, "key" VARCHAR(190) NOT NULL UNIQUE, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, processed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE outbox');
        $this->addSql('ALTER TABLE category DROP COLUMN published_at');
        $this->addSql('ALTER TABLE category DROP COLUMN published');
        $this->addSql('ALTER TABLE category DROP COLUMN workflow_state');
    }
}
