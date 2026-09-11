<?php

declare(strict_types=1);

namespace App\Cataloging\Migrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Exception\IrreversibleMigration;

final class Version20260821191000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Retire legacy Retailing source catalog bridges and unpublish legacy catalog roots.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Cataloging production schema requires PostgreSQL.');
        $this->abortIf(!$schema->hasTable('catalog') || !$schema->hasTable('category'), 'Cataloging tables are required.');

        $rows = $this->connection->fetchAllAssociative(<<<'SQL'
SELECT category.id, category.metadata::text AS metadata
FROM category
JOIN catalog ON catalog.id = category.catalog_id
WHERE catalog.object_code = 'retailing'
  AND catalog.tenant = 'default'
  AND category.parent_id IS NULL
  AND category.slug IN ('product', 'service', 'project', 'task', 'order')
SQL);

        foreach ($rows as $row) {
            $metadata = json_decode((string) $row['metadata'], true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($metadata)) {
                throw new \RuntimeException(sprintf('Retailing category %s metadata must be an object.', (string) $row['id']));
            }
            $clean = $this->stripLegacyKeys($metadata);
            $this->addSql(
                'UPDATE category SET metadata = CAST(:metadata AS json) WHERE id = :id',
                ['metadata' => json_encode($clean, JSON_THROW_ON_ERROR), 'id' => (int) $row['id']],
            );
        }

        $this->addSql(<<<'SQL'
UPDATE category
SET published = FALSE,
    published_at = NULL,
    workflow_state = 'draft'
WHERE catalog_id IN (
    SELECT id FROM catalog WHERE tenant = 'default' AND object_code IN ('services', 'products', 'projects')
)
SQL);

        if ($schema->hasTable('category_projection')) {
            $this->addSql(<<<'SQL'
UPDATE category_projection
SET published = FALSE,
    published_at = NULL,
    workflow_state = 'draft',
    updated_at = NOW()
WHERE id IN (
    SELECT category.id::text
    FROM category
    JOIN catalog ON catalog.id = category.catalog_id
    WHERE catalog.tenant = 'default'
      AND catalog.object_code IN ('services', 'products', 'projects')
)
SQL);
        }

        $this->addSql(<<<'SQL'
UPDATE catalog
SET object_active = FALSE,
    object_enabled = FALSE,
    object_status = 'inactive',
    object_modified_at = NOW()
WHERE tenant = 'default'
  AND object_code IN ('services', 'products', 'projects')
SQL);
    }

    public function down(Schema $schema): void
    {
        throw new IrreversibleMigration('Legacy Retailing catalog bridges are intentionally retired and historical rows are preserved unpublished.');
    }

    /** @param array<string, mixed> $value @return array<string, mixed> */
    private function stripLegacyKeys(array $value): array
    {
        unset($value['sourceCategoryId'], $value['sourceCatalog']);

        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $value[$key] = array_is_list($item)
                    ? array_map(fn (mixed $entry): mixed => is_array($entry) ? $this->stripLegacyKeys($entry) : $entry, $item)
                    : $this->stripLegacyKeys($item);
            }
        }

        return $value;
    }
}
