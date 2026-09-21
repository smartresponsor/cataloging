<?php

declare(strict_types=1);

namespace App\Cataloging\Migrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260823015200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add icon_url to the canonical category projection read model.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform,
            'Cataloging category projection icon migration supports PostgreSQL only.',
        );

        $exists = 1 === (int) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = 'public' AND table_name = 'category_projection' AND column_name = 'icon_url'",
        );
        if (!$exists) {
            $this->addSql('ALTER TABLE category_projection ADD icon_url VARCHAR(255) DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException(
            'Cataloging projection icon data is retained in production-safe migrations.',
        );
    }
}
