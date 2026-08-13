<?php

declare(strict_types=1);

namespace App\Cataloging\Controller\Catalog;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final readonly class CatalogCatalogReadController
{
    public function __construct(private Connection $connection)
    {
    }

    #[Route(
        '/api/catalog/{catalogCode}/category/tree',
        name: 'api_catalog_category_tree_by_code',
        requirements: ['catalogCode' => '[a-z0-9][a-z0-9-]{1,63}'],
        methods: ['GET'],
    )]
    public function tree(string $catalogCode): JsonResponse
    {
        $catalog = $this->connection->fetchAssociative(
            'SELECT id, object_code, name, purpose FROM catalog WHERE object_code = :code AND tenant = :tenant LIMIT 1',
            ['code' => $catalogCode, 'tenant' => 'default'],
        );

        if (false === $catalog) {
            return new JsonResponse(['ok' => false, 'error' => 'catalog_not_found'], 404);
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
        foreach ($nodes as $id => &$node) {
            $parentId = $node['parentNodeId'];
            if (null !== $parentId && isset($nodes[$parentId])) {
                $nodes[$parentId]['children'][] = &$node;
            } else {
                $tree[] = &$node;
            }
        }
        unset($node);

        $root = $tree[0] ?? null;

        return new JsonResponse([
            'ok' => true,
            'catalog' => [
                'code' => (string) $catalog['object_code'],
                'name' => (string) $catalog['name'],
                'purpose' => (string) $catalog['purpose'],
            ],
            'root' => $root,
            'nodes' => is_array($root) ? $root['children'] : [],
        ]);
    }
}
