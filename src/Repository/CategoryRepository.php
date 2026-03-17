<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Repository;

use App\RepositoryInterface\CategoryRepositoryInterface;

/**
 * SQL-backed repository. Actual SQL and connections are injected in infrastructure layer.
 * Here we keep contract-level shape and safe defaults.
 */
final class CategoryRepository implements CategoryRepositoryInterface
{
    /** @var array<string, array<string, mixed>> */
    private array $items = [];

    private int $sequence = 1;

    /** @return array<string, array<string, mixed>> */
    public function exportState(): array
    {
        return $this->items;
    }

    /** @param array<string, array<string, mixed>> $items */
    public function importState(array $items): void
    {
        $normalized = [];
        $max = 0;

        foreach ($items as $id => $item) {
            $key = (string) ($item['id'] ?? $id);
            $normalized[$key] = [
                'id' => $key,
                'taxonomyId' => (string) ($item['taxonomyId'] ?? 'catalog'),
                'parentId' => $item['parentId'] ?? null,
                'name' => is_array($item['name'] ?? null) ? $item['name'] : ['en' => (string) ($item['name'] ?? $key)],
                'slug' => is_array($item['slug'] ?? null) ? $item['slug'] : ['en' => (string) ($item['slug'] ?? $key)],
                'meta' => is_array($item['meta'] ?? null) ? $item['meta'] : [],
                'order' => (int) ($item['order'] ?? 0),
                'path' => (string) ($item['path'] ?? ''),
            ];
            if ('' === $normalized[$key]['path']) {
                $normalized[$key]['path'] = $this->buildPath($normalized[$key]['parentId'], $normalized[$key]['slug']);
            }
            if (preg_match('/(\d+)$/', $key, $m)) {
                $max = max($max, (int) $m[1]);
            }
        }

        $this->items = $normalized;
        $this->sequence = max(1, $max + 1);
    }

    public function seed(array $items): void
    {
        $this->items = [];
        $max = 0;

        foreach ($items as $item) {
            $id = (string) $item['id'];
            $this->items[$id] = [
                'id' => $id,
                'taxonomyId' => (string) ($item['taxonomyId'] ?? 'catalog'),
                'parentId' => $item['parentId'] ?? null,
                'name' => $item['name'] ?? ['en' => $id],
                'slug' => $item['slug'] ?? ['en' => $id],
                'meta' => $item['meta'] ?? [],
                'order' => (int) ($item['order'] ?? 0),
                'path' => '',
            ];
            if (preg_match('/(\d+)$/', $id, $m)) {
                $max = max($max, (int) $m[1]);
            }
        }

        foreach (array_keys($this->items) as $id) {
            $this->items[$id]['path'] = $this->buildPath($this->items[$id]['parentId'], $this->items[$id]['slug']);
        }

        $this->sequence = max($this->sequence, $max + 1);
    }

    public function findById(string $categoryId, string $locale = 'en'): array
    {
        if (!isset($this->items[$categoryId])) {
            return [];
        }

        return $this->normalizeLocalizedRow($this->items[$categoryId], $locale);
    }

    public function childrenOf(string $categoryId, string $locale = 'en', bool $publishedOnly = true): array
    {
        $rows = [];
        foreach ($this->items as $item) {
            if ($item['parentId'] !== $categoryId) {
                continue;
            }
            if ($publishedOnly && !$this->isPublished($item)) {
                continue;
            }
            $rows[] = $this->normalizeLocalizedRow($item, $locale);
        }

        usort($rows, static fn (array $a, array $b): int => [$a['order'], $a['id']] <=> [$b['order'], $b['id']]);

        return array_values($rows);
    }

    public function ancestorsOf(string $categoryId, string $locale = 'en'): array
    {
        return $this->breadcrumb($categoryId, $locale);
    }

    public function publishedTree(string $taxonomyCode, ?string $parentId, int $depth, string $locale): array
    {
        return array_values(array_filter(
            $this->tree($taxonomyCode, $parentId, $depth, $locale),
            fn (array $row): bool => (bool) (($row['meta']['published'] ?? true) === true)
        ));
    }

    public function tree(string $taxonomyCode, ?string $parentId, int $depth, string $locale): array
    {
        $rows = [];

        foreach ($this->items as $item) {
            if ($item['taxonomyId'] !== $taxonomyCode) {
                continue;
            }
            if ($item['parentId'] !== $parentId) {
                continue;
            }

            $rows[] = $this->normalizeLocalizedRow($item, $locale);

            if ($depth > 1) {
                foreach ($this->collectDescendants((string) $item['id'], $taxonomyCode, $depth - 1, $locale) as $descendant) {
                    $rows[] = $descendant;
                }
            }
        }

        usort($rows, static fn (array $a, array $b): int => [$a['order'], $a['id']] <=> [$b['order'], $b['id']]);

        return array_values($rows);
    }

    public function breadcrumb(string $categoryId, string $locale): array
    {
        if (!isset($this->items[$categoryId])) {
            return [];
        }

        $chain = [];
        $current = $this->items[$categoryId];
        while (true) {
            array_unshift($chain, $this->normalizeLocalizedRow($current, $locale));
            $parentId = $current['parentId'];
            if (null === $parentId || !isset($this->items[$parentId])) {
                break;
            }
            $current = $this->items[$parentId];
        }

        return $chain;
    }

    public function slugExists(string $slug, string $taxonomyId, ?string $parentId, string $locale): bool
    {
        foreach ($this->items as $item) {
            if ($item['taxonomyId'] !== $taxonomyId || $item['parentId'] !== $parentId) {
                continue;
            }
            if (($item['slug'][$locale] ?? null) === $slug) {
                return true;
            }
        }

        return false;
    }

    public function create(string $taxonomyId, ?string $parentId, array $name, array $slug, array $meta): array
    {
        $id = sprintf('cat-%d', $this->sequence++);
        $item = [
            'id' => $id,
            'taxonomyId' => $taxonomyId,
            'parentId' => $parentId,
            'name' => $name,
            'slug' => $slug,
            'meta' => $meta,
            'path' => $this->buildPath($parentId, $slug),
            'order' => 0,
        ];

        $this->items[$id] = $item;

        return $item;
    }

    public function move(string $actorId, string $categoryId, ?string $newParentId, int $newOrder): array
    {
        if (!isset($this->items[$categoryId])) {
            return ['id' => $categoryId, 'parentId' => $newParentId, 'order' => $newOrder];
        }

        $this->items[$categoryId]['parentId'] = $newParentId;
        $this->items[$categoryId]['order'] = $newOrder;
        $this->items[$categoryId]['path'] = $this->buildPath($newParentId, $this->items[$categoryId]['slug']);
        $this->items[$categoryId]['meta']['updatedAt'] = gmdate('c');
        $this->items[$categoryId]['meta']['updatedBy'] = $actorId;

        return [
            'id' => $categoryId,
            'parentId' => $newParentId,
            'order' => $newOrder,
            'path' => $this->items[$categoryId]['path'],
            'meta' => $this->items[$categoryId]['meta'],
        ];
    }

    public function setPublished(string $categoryId, bool $published, string $actorId = 'system'): array
    {
        if (!isset($this->items[$categoryId])) {
            return [];
        }

        $this->items[$categoryId]['meta']['published'] = $published;
        $this->items[$categoryId]['meta']['state'] = $published ? 'published' : 'draft';
        $this->items[$categoryId]['meta']['updatedAt'] = gmdate('c');
        $this->items[$categoryId]['meta']['updatedBy'] = $actorId;

        return $this->items[$categoryId];
    }

    public function bulkSetPublished(array $categoryIds, bool $published, string $actorId = 'system'): array
    {
        $result = ['success' => [], 'failed' => []];

        foreach ($categoryIds as $categoryId) {
            $id = is_scalar($categoryId) ? trim((string) $categoryId) : '';
            if ('' === $id || !isset($this->items[$id])) {
                $result['failed'][] = ['id' => $categoryId, 'reason' => 'not_found'];
                continue;
            }

            $updated = $this->setPublished($id, $published, $actorId);
            $result['success'][] = [
                'id' => $id,
                'action' => $published ? 'publish' : 'unpublish',
                'published' => $published,
                'path' => $updated['path'],
            ];
        }

        return $result;
    }

    public function attach(string $actorId, string $categoryId, string $targetDomain, string $targetClass, string $targetId): void
    {
    }

    public function detach(string $actorId, string $categoryId, string $targetDomain, string $targetClass, string $targetId): void
    {
    }

    public function resolve(string $taxonomyCode, string $targetDomain, string $targetId, string $locale): array
    {
        return [];
    }

    public function bySlug(string $taxonomyCode, string $slug, string $locale): array
    {
        foreach ($this->items as $item) {
            if ($item['taxonomyId'] !== $taxonomyCode) {
                continue;
            }
            if (($item['slug'][$locale] ?? null) === $slug) {
                return $this->normalizeLocalizedRow($item, $locale);
            }
        }

        return [];
    }

    public function fullSlug(string $categoryId, string $locale): string
    {
        if (!isset($this->items[$categoryId])) {
            return '';
        }

        return $this->buildPath($this->items[$categoryId]['parentId'], $this->items[$categoryId]['slug'], $locale);
    }

    /** @return array<int, array<string, mixed>> */
    private function collectDescendants(string $parentId, string $taxonomyId, int $depth, string $locale): array
    {
        if ($depth <= 0) {
            return [];
        }

        $rows = [];
        foreach ($this->items as $item) {
            if ($item['taxonomyId'] !== $taxonomyId || $item['parentId'] !== $parentId) {
                continue;
            }
            $rows[] = $this->normalizeLocalizedRow($item, $locale);
            foreach ($this->collectDescendants((string) $item['id'], $taxonomyId, $depth - 1, $locale) as $descendant) {
                $rows[] = $descendant;
            }
        }

        return $rows;
    }

    /** @return array<string, mixed> */
    private function normalizeLocalizedRow(array $item, string $locale): array
    {
        return [
            'id' => $item['id'],
            'taxonomyId' => $item['taxonomyId'],
            'parentId' => $item['parentId'],
            'name' => $item['name'][$locale] ?? reset($item['name']) ?: '',
            'slug' => $item['slug'][$locale] ?? reset($item['slug']) ?: '',
            'meta' => $item['meta'],
            'path' => $this->buildPath($item['parentId'], $item['slug'], $locale),
            'order' => $item['order'],
        ];
    }

    private function buildPath(?string $parentId, array $slug, string $locale = 'en'): string
    {
        $selfSlug = (string) ($slug[$locale] ?? reset($slug) ?: '');
        if (null === $parentId || !isset($this->items[$parentId])) {
            return '/'.$selfSlug;
        }

        $parentPath = $this->buildPath($this->items[$parentId]['parentId'], $this->items[$parentId]['slug'], $locale);

        return rtrim($parentPath, '/').'/'.$selfSlug;
    }

    private function isPublished(array $item): bool
    {
        return (bool) (($item['meta']['published'] ?? true) === true);
    }
}
