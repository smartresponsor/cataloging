<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/** @noinspection PhpMissingParentCallCommonInspection */
final class Version20251102081000_category_projection_search_runtime_hardening extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add search-oriented indexes for category projection reads';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX idx_category_projection_name ON category_projection (name)');
        $this->addSql('CREATE INDEX idx_category_projection_slug ON category_projection (slug)');
        $this->addSql(
            'CREATE INDEX idx_category_projection_tenant_locale ON category_projection (tenant, locale)'
        );
        $this->addSql(
            'CREATE INDEX idx_category_projection_workflow_state ON category_projection (workflow_state)'
        );
        $this->addSql('CREATE INDEX idx_category_projection_updated_at ON category_projection (updated_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_category_projection_updated_at');
        $this->addSql('DROP INDEX idx_category_projection_workflow_state');
        $this->addSql('DROP INDEX idx_category_projection_tenant_locale');
        $this->addSql('DROP INDEX idx_category_projection_slug');
        $this->addSql('DROP INDEX idx_category_projection_name');
    }
}
