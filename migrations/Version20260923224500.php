<?php

declare(strict_types=1);

namespace App\Cataloging\Migrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260923224500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename the Catalog outbox key unique index from Doctrine hash naming to a deterministic semantic name.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform,
            'Cataloging outbox unique-index normalization supports PostgreSQL only.',
        );

        $this->addSql(
            <<<'SQL'
DO $$
BEGIN
    IF to_regclass('public.uniq_6ae7d5708a90aba9') IS NOT NULL AND to_regclass('public.uniq_outbox_key') IS NULL THEN
        ALTER INDEX uniq_6ae7d5708a90aba9 RENAME TO uniq_outbox_key;
    ELSIF to_regclass('public.uniq_6ae7d5708a90aba9') IS NOT NULL AND to_regclass('public.uniq_outbox_key') IS NOT NULL THEN
        RAISE EXCEPTION 'Both legacy outbox key index and canonical outbox key index exist; manual reconciliation is required.';
    END IF;
END
$$
SQL,
        );
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException(
            'Deterministic Catalog outbox key constraint naming is intentionally irreversible.',
        );
    }
}

