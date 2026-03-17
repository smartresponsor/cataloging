<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service;

use App\Service\CatalogCategory\DraftPolicy;
use App\Service\CatalogCategory\PublishOperation;
use App\Service\CatalogCategory\Status;
use App\Service\CatalogCategory\TreeOperation;

final class GraphqlResolver implements GraphqlResolverInterface
{
    private PublishOperation $publish;
    private TreeOperation $tree;

    public function __construct(?PublishOperation $publish = null, ?TreeOperation $tree = null)
    {
        $this->publish = $publish ?? new PublishOperation(new DraftPolicy());
        $this->tree = $tree ?? new TreeOperation();
    }

    public function category(array $args): ?array
    {
        // Application layer should fetch by ID from read model.
        // Placeholder shape: return null when not found.
        $id = (string) ($args['id'] ?? '');
        if ('' === $id) {
            return null;
        }

        return [
            'id' => $id,
            'parentId' => null,
            'slug' => 'demo',
            'name' => 'Demo',
            'locale' => 'en',
            'status' => 'published',
        ];
    }

    public function categoryPath(array $args): array
    {
        $id = (string) ($args['id'] ?? '');
        if ('' === $id) {
            return [];
        }

        // Application layer should resolve path from projection (breadcrumb).
        return [[
            'id' => $id,
            'parentId' => null,
            'slug' => 'demo',
            'name' => 'Demo',
            'locale' => 'en',
            'status' => 'published',
        ]];
    }

    public function publishCategory(array $args): ?array
    {
        $input = (array) ($args['input'] ?? []);
        $id = (string) ($input['id'] ?? '');
        if ('' === $id) {
            return null;
        }
        $status = new Status(Status::DRAFT);
        $new = $this->publish->publish($status);

        return [
            'id' => $id,
            'parentId' => null,
            'slug' => 'demo',
            'name' => 'Demo',
            'locale' => 'en',
            'status' => $new->value(),
        ];
    }

    public function moveCategory(array $args): bool
    {
        $input = (array) ($args['input'] ?? []);
        $id = (string) ($input['id'] ?? '');
        $parentId = isset($input['parentId']) ? (string) $input['parentId'] : null;
        if ('' === $id) {
            return false;
        }
        $this->tree->move($id, $parentId);

        return true;
    }
}
