<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Service\Api\Category;

use App\Service\Command\Category\TreeOperation;
use App\Service\Query\Category\Status;
use App\Service\Security\Category\DraftPolicy;
use App\Service\Workflow\Category\PublishOperation;
use App\ServiceInterface\Api\Category\GraphqlResolverInterface;

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

        $newStatus = $this->publish->publish(new Status(Status::DRAFT));

        return [
            'id' => $id,
            'parentId' => null,
            'slug' => 'demo',
            'name' => 'Demo',
            'locale' => 'en',
            'status' => $newStatus->value(),
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
