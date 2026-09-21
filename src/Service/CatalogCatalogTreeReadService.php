<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ServiceInterface\CatalogCatalogTreeReadServiceInterface;
use Doctrine\DBAL\Connection;

final readonly class CatalogCatalogTreeReadService implements CatalogCatalogTreeReadServiceInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public function byCode(string $catalogCode, string $tenant = 'default'): ?array
    {
        $normalizedCode = strtolower(trim($catalogCode));
        $normalizedTenant = '' === trim($tenant) ? 'default' : trim($tenant);
        if ('' === $normalizedCode) {
            return null;
        }

        $catalog = $this->connection->fetchAssociative(
            'SELECT id, object_code, name, purpose FROM catalog WHERE object_code = :code AND tenant = :tenant LIMIT 1',
            ['code' => $normalizedCode, 'tenant' => $normalizedTenant],
        );
        if (false === $catalog) {
            return null;
        }

        $rows = $this->connection->fetchAllAssociative(
            <<<'SQL'
                SELECT id, parent_id, name_entity, slug, depth, path, icon_url
                FROM category
                WHERE catalog_id = :catalogId
                  AND published = TRUE
                  AND workflow_state = 'published'
                ORDER BY depth ASC, path ASC
                SQL,
            ['catalogId' => $catalog['id']],
        );

        $nodes = [];
        foreach ($rows as $row) {
            $id = self::stringValue($row['id'] ?? null);
            if ('' === $id) {
                continue;
            }
            $nodes[$id] = [
                'nodeId' => $id,
                'parentNodeId' => self::nullableStringValue($row['parent_id'] ?? null),
                'title' => self::stringValue($row['name_entity'] ?? null),
                'slug' => self::stringValue($row['slug'] ?? null),
                'depth' => self::intValue($row['depth'] ?? null),
                'path' => self::stringValue($row['path'] ?? null),
                'iconUrl' => self::nullableStringValue($row['icon_url'] ?? null),
                'childCount' => 0,
                'children' => [],
            ];
        }

        foreach ($nodes as $node) {
            $parentId = $node['parentNodeId'];
            if (null !== $parentId && isset($nodes[$parentId])) {
                ++$nodes[$parentId]['childCount'];
            }
        }

        $tree = [];
        foreach ($nodes as &$node) {
            $parentId = $node['parentNodeId'];
            if (null !== $parentId && isset($nodes[$parentId])) {
                $nodes[$parentId]['children'][] = &$node;
            } else {
                $tree[] = &$node;
            }
        }
        unset($node);

        $root = $tree[0] ?? null;

        return [
            'catalog' => [
                'code' => self::stringValue($catalog['object_code'] ?? null),
                'name' => self::stringValue($catalog['name'] ?? null),
                'purpose' => self::stringValue($catalog['purpose'] ?? null),
            ],
            'root' => $root,
            'nodes' => is_array($root) ? $root['children'] : [],
        ];
    }

    private static function stringValue(mixed $value, string $default = ''): string
    {
        return is_scalar($value) ? (string) $value : $default;
    }

    private static function nullableStringValue(mixed $value): ?string
    {
        return null === $value ? null : (is_scalar($value) ? (string) $value : null);
    }

    private static function intValue(mixed $value): int
    {
        return is_int($value) ? $value : (is_scalar($value) && is_numeric((string) $value) ? (int) $value : 0);
    }
}
