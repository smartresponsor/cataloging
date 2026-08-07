<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260807002300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add icon URL to the durable category projection read model.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category_projection ADD COLUMN icon_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category_projection DROP COLUMN icon_url');
    }
}
