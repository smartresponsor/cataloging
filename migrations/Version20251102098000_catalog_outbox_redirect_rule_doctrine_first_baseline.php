<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251102098000_catalog_outbox_redirect_rule_doctrine_first_baseline extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Doctrine-first baseline for redirect_rule and supporting outbox uniqueness';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS redirect_rule (from_path VARCHAR(2048) NOT NULL, to_path VARCHAR(2048) NOT NULL, locale VARCHAR(16) DEFAULT NULL, source VARCHAR(64) NOT NULL, PRIMARY KEY(from_path))');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS uniq_redirect_rule_locale_from ON redirect_rule (locale, from_path)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS uniq_outbox_key ON outbox ("key")');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IF EXISTS uniq_redirect_rule_locale_from');
        $this->addSql('DROP TABLE IF EXISTS redirect_rule');
        $this->addSql('DROP INDEX IF EXISTS uniq_outbox_key');
    }
}
