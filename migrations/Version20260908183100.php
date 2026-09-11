<?php

declare(strict_types=1);

namespace App\Cataloging\Migrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260908183100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Flatten Objecting physical columns and remove Cataloging system-field duplication.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Cataloging production schema requires PostgreSQL.');

        $this->renameColumnIfPresent('category', 'slug', 'category_slug');
        $this->renameColumnIfPresent('category_translation', 'slug', 'translation_slug');

        foreach (['category_translation', 'category_attachment_translation'] as $table) {
            if ($this->columnExists($table, 'locale') && $this->columnExists($table, 'object_locale')) {
                $this->addSql(sprintf('UPDATE %s SET object_locale = locale WHERE object_locale IS DISTINCT FROM locale', $table));
                $this->addSql(sprintf('ALTER TABLE %s DROP COLUMN locale', $table));
            }
        }

        if ($this->columnExists('category_featured', 'active') && $this->columnExists('category_featured', 'object_active')) {
            $this->addSql("UPDATE category_featured SET object_active = active, object_enabled = active, object_status = CASE WHEN active THEN 'active' ELSE 'inactive' END");
            $this->addSql('ALTER TABLE category_featured DROP COLUMN active');
        }

        $packs = [
            'catalog' => ['identity', 'title', 'audit', 'code', 'state'],
            'category' => ['identity', 'title', 'audit', 'state'],
            'category_featured' => ['identity', 'audit', 'state'],
            'category_product_binding' => ['identity', 'audit', 'state'],
            'category_translation' => ['identity', 'audit', 'locale'],
            'category_attachment_translation' => ['identity', 'audit', 'locale'],
        ];

        foreach ($packs as $table => $selected) {
            if (!$this->tableExists($table)) {
                continue;
            }

            $columns = [];
            if (in_array('identity', $selected, true)) {
                $columns += ['object_uuid' => 'uuid', 'object_slug' => 'slug'];
            }
            if (in_array('title', $selected, true)) {
                $columns += ['object_first_title' => 'first_title', 'object_middle_title' => 'middle_title', 'object_last_title' => 'last_title'];
            }
            if (in_array('audit', $selected, true)) {
                $columns += ['object_created_at' => 'created_at', 'object_modified_at' => 'modified_at', 'object_created_by' => 'created_by', 'object_modified_by' => 'modified_by'];
            }
            if (in_array('code', $selected, true)) {
                $columns += ['object_code' => 'code'];
            }
            if (in_array('state', $selected, true)) {
                $columns += ['object_active' => 'active', 'object_enabled' => 'enabled', 'object_status' => 'status'];
            }
            if (in_array('locale', $selected, true)) {
                $columns += ['object_locale' => 'locale', 'object_timezone' => 'timezone'];
            }

            foreach ($columns as $from => $to) {
                $this->renameColumnIfPresent($table, $from, $to);
            }
        }
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('The flat Objecting physical-column canon is a forward-only schema transition.');
    }

    private function tableExists(string $table): bool
    {
        return 1 === (int) $this->connection->fetchOne("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public' AND table_name = ?", [$table]);
    }

    private function columnExists(string $table, string $column): bool
    {
        return 1 === (int) $this->connection->fetchOne("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = 'public' AND table_name = ? AND column_name = ?", [$table, $column]);
    }

    private function renameColumnIfPresent(string $table, string $from, string $to): void
    {
        if ($this->columnExists($table, $from) && !$this->columnExists($table, $to)) {
            $this->addSql(sprintf('ALTER TABLE %s RENAME COLUMN %s TO %s', $table, $from, $to));
        }
    }
}
