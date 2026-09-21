<?php

declare(strict_types=1);

namespace App\Cataloging\Migrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260820161500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add structured metadata to Cataloging categories.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform,
            'Cataloging category metadata migration supports PostgreSQL only.',
        );

        $exists = 1 === (int) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = 'public' AND table_name = 'category' AND column_name = 'metadata'",
        );
        if (!$exists) {
            $this->addSql("ALTER TABLE category ADD metadata JSON NOT NULL DEFAULT '{}'::json");
        }
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Cataloging category metadata is retained in production-safe migrations.');
    }
}
