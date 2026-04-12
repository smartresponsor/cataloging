<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Outbox projection runtime hardening migration.
 *
 * @noinspection PhpClassNamingConventionInspection
 */
/** @noinspection PhpCSFixerValidationInspection */
final class Version20251102073500_outbox_projection_runtime_hardening extends AbstractMigration
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    public function getDescription(): string
    {
        return 'Add outbox retry and projection processing fields for runtime hardening';
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function up(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE outbox ADD COLUMN available_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL'
        );
        $this->addSql('UPDATE outbox SET available_at = created_at WHERE available_at IS NULL');
        $this->addSql('ALTER TABLE outbox ALTER COLUMN available_at SET NOT NULL');
        $this->addSql('ALTER TABLE outbox ADD COLUMN attempts INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE outbox ADD COLUMN last_error TEXT DEFAULT NULL');
        $this->addSql(
            'ALTER TABLE outbox ADD COLUMN dispatched_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL'
        );
        $this->addSql(
            'ALTER TABLE outbox ADD COLUMN dead_lettered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL'
        );
        $this->addSql(
            'CREATE INDEX idx_outbox_projection_ready ON outbox '.
            '(processed_at, dead_lettered_at, available_at, created_at)'
        );
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_outbox_projection_ready');
        $this->addSql('ALTER TABLE outbox DROP COLUMN dead_lettered_at');
        $this->addSql('ALTER TABLE outbox DROP COLUMN dispatched_at');
        $this->addSql('ALTER TABLE outbox DROP COLUMN last_error');
        $this->addSql('ALTER TABLE outbox DROP COLUMN attempts');
        $this->addSql('ALTER TABLE outbox DROP COLUMN available_at');
    }
}
