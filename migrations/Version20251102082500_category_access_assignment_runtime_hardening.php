<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Category access assignment runtime hardening migration.
 *
 * @noinspection PhpClassNamingConventionInspection
 */
final class Version20251102082500_category_access_assignment_runtime_hardening extends AbstractMigration
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    public function getDescription(): string
    {
        return 'Add durable category access assignment storage for policy-based mutation authorization.';
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
CREATE TABLE category_access_assignment (
  assignment_id VARCHAR(64) NOT NULL,
  category_id VARCHAR(64) NOT NULL,
  actor_user_id VARCHAR(191) NOT NULL,
  role VARCHAR(32) NOT NULL,
  status VARCHAR(16) NOT NULL,
  is_primary TINYINT(1) NOT NULL DEFAULT 0,
  granted_at DATETIME NOT NULL,
  revoked_at DATETIME DEFAULT NULL,
  PRIMARY KEY (assignment_id),
  UNIQUE KEY uniq_category_access_assignment_actor (category_id, actor_user_id),
  KEY idx_category_access_assignment_category_status (category_id, status),
  KEY idx_category_access_assignment_actor_status (actor_user_id, status)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL);
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE category_access_assignment');
    }
}
