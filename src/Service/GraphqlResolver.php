<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Service\Category\DraftPolicy;
use App\Service\Category\PublishOperation;
use App\Service\Category\Status;
use App\Service\Category\TreeOperation;

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
        $id = trim((string) ($args['id'] ?? ''));
        if ('' === $id) {
            return null;
        }

        return $this->normalizeNode($id, null, 'published');
    }

    public function categoryPath(array $args): array
    {
        $id = trim((string) ($args['id'] ?? ''));
        if ('' === $id) {
            return [];
        }

        return [$this->normalizeNode($id, null, 'published')];
    }

    public function publishCategory(array $args): ?array
    {
        $input = (array) ($args['input'] ?? []);
        $id = trim((string) ($input['id'] ?? ''));
        if ('' === $id) {
            return null;
        }

        $status = new Status(Status::DRAFT);
        $published = $this->publish->publish($status);

        return $this->normalizeNode($id, null, $published->value());
    }

    public function moveCategory(array $args): bool
    {
        $input = (array) ($args['input'] ?? []);
        $id = trim((string) ($input['id'] ?? ''));
        $parentId = array_key_exists('parentId', $input) && null !== $input['parentId']
            ? trim((string) $input['parentId'])
            : null;

        if ('' === $id) {
            return false;
        }

        $this->tree->move($id, $parentId ?: null);

        return true;
    }

    private function normalizeNode(string $id, ?string $parentId, string $status): array
    {
        return [
            'id' => $id,
            'parentId' => $parentId,
            'slug' => strtolower($id),
            'name' => ucfirst(str_replace(['-', '_'], ' ', $id)),
            'locale' => 'en',
            'status' => $status,
        ];
    }
}
