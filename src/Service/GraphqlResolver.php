<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\GraphqlResolverInterface;

final class GraphqlResolver implements GraphqlResolverInterface
{
    private PublishOperation $publish;
    private TreeOperation $tree;

    public function __construct(?PublishOperation $publish = null, ?TreeOperation $tree = null)
    {
        $this->publish = $publish ?? new PublishOperation(new DraftPolicy());
        $this->tree = $tree ?? new TreeOperation();
    }

    /** @param array<string,mixed> $args @return array<string,mixed>|null */
    public function category(array $args): ?array
    {
        $id = $this->stringValue($args, 'id');
        if ('' === $id) {
            return null;
        }

        return $this->normalizeNode($id, null, 'published');
    }

    /** @param array<string,mixed> $args @return list<array<string,mixed>> */
    public function categoryPath(array $args): array
    {
        $id = $this->stringValue($args, 'id');
        if ('' === $id) {
            return [];
        }

        return [$this->normalizeNode($id, null, 'published')];
    }

    /** @param array<string,mixed> $args @return array<string,mixed>|null */
    public function publishCategory(array $args): ?array
    {
        $input = $this->arrayValue($args, 'input');
        $id = $this->stringValue($input, 'id');
        if ('' === $id) {
            return null;
        }

        $status = new Status(Status::DRAFT);
        $published = $this->publish->publish($status);

        return $this->normalizeNode($id, null, $published->value());
    }

    /** @param array<string,mixed> $args */
    public function moveCategory(array $args): bool
    {
        $input = $this->arrayValue($args, 'input');
        $id = $this->stringValue($input, 'id');
        $parentId = $this->nullableStringValue($input, 'parentId');

        if ('' === $id) {
            return false;
        }

        $this->tree->move($id, $parentId);

        return true;
    }

    /** @return array<string,mixed> */
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

    /** @param array<string,mixed> $input */
    private function stringValue(array $input, string $key, string $default = ''): string
    {
        $value = $input[$key] ?? $default;

        return is_scalar($value) ? trim((string) $value) : $default;
    }

    /** @param array<string,mixed> $input */
    private function nullableStringValue(array $input, string $key): ?string
    {
        if (!array_key_exists($key, $input) || null === $input[$key]) {
            return null;
        }
        $value = $input[$key];

        return is_scalar($value) ? trim((string) $value) : null;
    }

    /**
     * @param array<string,mixed> $input
     *
     * @return array<string,mixed>
     */
    private function arrayValue(array $input, string $key): array
    {
        $value = $input[$key] ?? [];

        return is_array($value) ? $value : [];
    }
}
