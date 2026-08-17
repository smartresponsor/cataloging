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
            $id = (string) $row['id'];
            $nodes[$id] = [
                'nodeId' => $id,
                'parentNodeId' => null === $row['parent_id'] ? null : (string) $row['parent_id'],
                'title' => (string) $row['name_entity'],
                'slug' => (string) $row['slug'],
                'depth' => (int) $row['depth'],
                'path' => (string) $row['path'],
                'iconUrl' => $row['icon_url'],
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
                'code' => (string) $catalog['object_code'],
                'name' => (string) $catalog['name'],
                'purpose' => (string) $catalog['purpose'],
            ],
            'root' => $root,
            'nodes' => is_array($root) ? $root['children'] : [],
        ];
    }
}
