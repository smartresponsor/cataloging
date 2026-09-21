<?php

declare(strict_types=1);

namespace App\Cataloging\Migrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260813060000 extends AbstractMigration
{
    private const PROJECTION = __DIR__.'/cataloging_entity_projection_20260813.sql';

    public function getDescription(): string
    {
        return 'Cataloging entity-first production readiness adopt-or-create baseline.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform,
            'Cataloging production readiness migration supports PostgreSQL only.',
        );

        if (!$this->tableExists('catalog') && !$this->tableExists('category')) {
            $this->runProjectionBootstrap();
            return;
        }

        $this->abortIf(!$this->tableExists('catalog'), 'Cataloging adoption requires existing catalog table.');
        $this->abortIf(!$this->tableExists('category'), 'Cataloging adoption requires existing category table.');
        $this->abortIf(!$this->columnExists('category', 'catalog_id'), 'Cataloging adoption requires category.catalog_id.');
        $this->abortIf(!$this->columnExists('category', 'parent_id'), 'Cataloging adoption requires category.parent_id.');

        $this->assertParentCompatibility();
        $this->addObjectingColumns('catalog', true, true, true, 'name');
        $this->addObjectingColumns('category', true, true, true, 'name_entity');
        $this->convertCategoryParentId();
        $this->addCategoryConstraints();
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Cataloging adoption can preserve production data only in the up direction.');
    }

    private function runProjectionBootstrap(): void
    {
        $sql = @file_get_contents(self::PROJECTION);
        $this->abortIf(false === $sql || '' === trim($sql), 'Cataloging frozen entity projection is missing.');

        foreach (array_filter(array_map('trim', explode(";", $sql))) as $statement) {
            if ('' !== $statement) {
                $this->addSql($statement);
            }
        }
    }

    private function assertParentCompatibility(): void
    {
        $this->abortIf(0 < $this->countSql("SELECT COUNT(*) FROM category WHERE parent_id IS NOT NULL AND btrim(parent_id::text) <> '' AND btrim(parent_id::text) !~ '^[0-9]+$'"), 'category.parent_id contains non-numeric values.');
        $this->abortIf(0 < $this->countSql("SELECT COUNT(*) FROM category WHERE parent_id IS NOT NULL AND btrim(parent_id::text) = '0'"), 'category.parent_id contains zero parent references.');
        $this->abortIf(0 < $this->countSql("SELECT COUNT(*) FROM category WHERE parent_id IS NOT NULL AND btrim(parent_id::text) ~ '^[0-9]+$' AND id = btrim(parent_id::text)::integer"), 'category.parent_id contains self references.');
        $this->abortIf(0 < $this->countSql("SELECT COUNT(*) FROM category c WHERE c.parent_id IS NOT NULL AND btrim(c.parent_id::text) ~ '^[0-9]+$' AND NOT EXISTS (SELECT 1 FROM category p WHERE p.id = btrim(c.parent_id::text)::integer)"), 'category.parent_id contains orphan references.');
        $this->abortIf(0 < $this->countSql("SELECT COUNT(*) FROM category c JOIN category p ON p.id = btrim(c.parent_id::text)::integer WHERE c.parent_id IS NOT NULL AND btrim(c.parent_id::text) ~ '^[0-9]+$' AND c.catalog_id <> p.catalog_id"), 'category.parent_id crosses catalog ownership.');
    }

    private function addObjectingColumns(string $table, bool $identity, bool $title, bool $state, string $titleSource): void
    {
        if ($identity) {
            $this->addColumnIfMissing($table, 'object_uuid', 'BYTEA');
            $this->addColumnIfMissing($table, 'object_slug', 'VARCHAR(190)');
            $this->addSql(sprintf("UPDATE %s SET object_uuid = decode(md5('%s:' || id::text), 'hex') WHERE object_uuid IS NULL", $table, $table));
            $this->addSql(sprintf("UPDATE %s SET object_slug = '%s:' || id::text WHERE object_slug IS NULL OR btrim(object_slug) = ''", $table, $table));
            $this->addNotNullIfNullable($table, 'object_uuid');
            $this->addNotNullIfNullable($table, 'object_slug');
            $this->addUniqueIndexIfMissing($table, 'object_uuid', 'uniq_'.$table.'_object_uuid');
            $this->addUniqueIndexIfMissing($table, 'object_slug', 'uniq_'.$table.'_object_slug');
        }

        if ($title) {
            $this->addColumnIfMissing($table, 'object_first_title', 'VARCHAR(255)');
            $this->addColumnIfMissing($table, 'object_middle_title', 'TEXT');
            $this->addColumnIfMissing($table, 'object_last_title', 'TEXT');
            $this->addSql(sprintf('UPDATE %s SET object_first_title = %s WHERE object_first_title IS NULL', $table, $titleSource));
        }

        $this->addColumnIfMissing($table, 'object_created_at', 'TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addColumnIfMissing($table, 'object_modified_at', 'TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addColumnIfMissing($table, 'object_created_by', 'VARCHAR(190)');
        $this->addColumnIfMissing($table, 'object_modified_by', 'VARCHAR(190)');
        $this->addSql(sprintf('UPDATE %s SET object_created_at = CURRENT_TIMESTAMP WHERE object_created_at IS NULL', $table));
        $this->addNotNullIfNullable($table, 'object_created_at');

        if ($state) {
            $this->addColumnIfMissing($table, 'object_active', 'BOOLEAN DEFAULT true');
            $this->addColumnIfMissing($table, 'object_enabled', 'BOOLEAN DEFAULT true');
            $this->addColumnIfMissing($table, 'object_status', 'VARCHAR(64)');
            $this->addSql(sprintf('UPDATE %s SET object_active = true WHERE object_active IS NULL', $table));
            $this->addSql(sprintf('UPDATE %s SET object_enabled = true WHERE object_enabled IS NULL', $table));
            $this->addSql(sprintf("UPDATE %s SET object_status = 'active' WHERE object_status IS NULL", $table));
            $this->addNotNullIfNullable($table, 'object_active');
            $this->addNotNullIfNullable($table, 'object_enabled');
        }
    }

    private function convertCategoryParentId(): void
    {
        $type = (string) $this->connection->fetchOne("SELECT data_type FROM information_schema.columns WHERE table_schema = 'public' AND table_name = 'category' AND column_name = 'parent_id'");
        if ('integer' !== $type) {
            $this->addSql("ALTER TABLE category ALTER parent_id DROP DEFAULT");
            $this->addSql("ALTER TABLE category ALTER parent_id TYPE INT USING NULLIF(btrim(parent_id::text), '')::integer");
        }
    }

    private function addCategoryConstraints(): void
    {
        if (!$this->constraintExists('category', 'fk_category_parent')) {
            $this->addSql("ALTER TABLE category ADD CONSTRAINT fk_category_parent FOREIGN KEY (parent_id) REFERENCES category (id) ON DELETE RESTRICT NOT DEFERRABLE");
        }
        if (!$this->constraintExists('category', 'chk_category_parent_not_self')) {
            $this->addSql("ALTER TABLE category ADD CONSTRAINT chk_category_parent_not_self CHECK (parent_id IS NULL OR parent_id <> id)");
        }
        $this->addIndexIfMissing('category', 'idx_category_parent', 'parent_id');
        $this->addIndexIfMissing('category', 'idx_category_catalog_path', 'catalog_id, path');
        $this->addUniqueIndexIfMissing('category', 'catalog_id, parent_id, slug', 'uniq_category_catalog_parent_slug');
    }

    private function addColumnIfMissing(string $table, string $column, string $definition): void
    {
        if (!$this->columnExists($table, $column)) {
            $this->addSql(sprintf('ALTER TABLE %s ADD %s %s', $table, $column, $definition));
        }
    }

    private function addNotNullIfNullable(string $table, string $column): void
    {
        $this->addSql(sprintf('ALTER TABLE %s ALTER %s SET NOT NULL', $table, $column));
    }

    private function addIndexIfMissing(string $table, string $name, string $columns): void
    {
        if (!$this->indexExists($name)) {
            $this->addSql(sprintf('CREATE INDEX %s ON %s (%s)', $name, $table, $columns));
        }
    }

    private function addUniqueIndexIfMissing(string $table, string $columns, string $name): void
    {
        if (!$this->indexExists($name)) {
            $this->addSql(sprintf('CREATE UNIQUE INDEX %s ON %s (%s)', $name, $table, $columns));
        }
    }

    private function tableExists(string $table): bool
    {
        return 1 === (int) $this->connection->fetchOne("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public' AND table_name = ?", [$table]);
    }

    private function columnExists(string $table, string $column): bool
    {
        return 1 === (int) $this->connection->fetchOne("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = 'public' AND table_name = ? AND column_name = ?", [$table, $column]);
    }

    private function constraintExists(string $table, string $name): bool
    {
        return 1 === (int) $this->connection->fetchOne("SELECT COUNT(*) FROM information_schema.table_constraints WHERE table_schema = 'public' AND table_name = ? AND constraint_name = ?", [$table, $name]);
    }

    private function indexExists(string $name): bool
    {
        return 1 === (int) $this->connection->fetchOne("SELECT COUNT(*) FROM pg_indexes WHERE schemaname = 'public' AND indexname = ?", [$name]);
    }

    private function countSql(string $sql): int
    {
        return (int) $this->connection->fetchOne($sql);
}
}
