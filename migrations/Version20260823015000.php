<?php

declare(strict_types=1);

namespace App\Cataloging\Migrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260823015000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add the canonical category icon_url column required by CatalogCategoryEntity.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform,
            'Cataloging category icon migration supports PostgreSQL only.',
        );

        $exists = 1 === (int) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = 'public' AND table_name = 'category' AND column_name = 'icon_url'",
        );
        if (!$exists) {
            $this->addSql('ALTER TABLE category ADD icon_url VARCHAR(255) DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException(
            'Cataloging category icon data is retained in production-safe migrations.',
        );
    }
}
