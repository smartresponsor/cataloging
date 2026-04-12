<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Category idempotency runtime hardening migration.
 *
 * @noinspection PhpClassNamingConventionInspection
 */
final class Version20251102075000_category_idempotency_runtime_hardening extends AbstractMigration
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    public function getDescription(): string
    {
        return 'Add durable category idempotency store for mutation requests';
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
CREATE TABLE category_idempotency (
    idempotency_key VARCHAR(190) PRIMARY KEY,
    operation VARCHAR(64) NOT NULL,
    request_hash VARCHAR(64) NOT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    correlation_id VARCHAR(64) DEFAULT NULL
)
SQL);
        $this->addSql('CREATE INDEX idx_category_idempotency_expiry ON category_idempotency (expires_at)');
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_category_idempotency_expiry');
        $this->addSql('DROP TABLE category_idempotency');
    }
}
