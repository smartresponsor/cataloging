<?php

declare(strict_types=1);

/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Cataloging\Repository\Category;

use App\Cataloging\ServiceInterface\CatalogCategoryProjectionReadServiceInterface;
use App\Cataloging\ServiceInterface\CategoryReadRepositoryInterface;
use App\Cataloging\ValueObject\CategoryReadRepositoryListRequest;

/**
 * Adapts the durable Cataloging projection read model to the category read contract.
 */
final readonly class CatalogEmptyCategoryReadRepository implements CategoryReadRepositoryInterface
{
    public function __construct(
        private CatalogCategoryProjectionReadServiceInterface $projectionReadService,
    ) {
    }

    /**
     * @return array{
     *     edges: array<int, array{id: string, name: string, slug: string, depth: int, path: string}>,
     *     pageInfo: array{endCursor?: string, hasNextPage: bool},
     *     total?: int,
     *     approxTotal?: int,
     * }
     */
    public function list(CategoryReadRepositoryListRequest $request): array
    {
        $criteria = $request->criteria();
        $first = max(1, $criteria['first']);
        $rows = $this->filterRows($this->projectionReadService->list(), $criteria);
        $pagedRows = $this->pageRows($rows, $criteria['after']);
        $edges = array_map($this->normalizeEdge(...), array_slice($pagedRows, 0, $first));

        $result = [
            'edges' => $edges,
            'pageInfo' => [
                'hasNextPage' => count($pagedRows) > $first,
            ],
        ];

        if ([] !== $edges) {
            $result['pageInfo']['endCursor'] = $edges[array_key_last($edges)]['id'];
        }

        if ($request->withTotal()) {
            $result['total'] = count($rows);
        }

        if ($request->approxTotal()) {
            $result['approxTotal'] = count($rows);
        }

        return $result;
    }

    /**
     * @return array<int, array{id: string, name: string, slug: string}>
     */
    public function breadcrumb(string $id): array
    {
        $normalizedId = trim($id);
        if ('' === $normalizedId) {
            throw new \InvalidArgumentException('Category id must not be empty when reading a breadcrumb.');
        }

        $rowsById = [];
        foreach ($this->projectionReadService->list() as $row) {
            $rowId = $this->stringValue($row['id'] ?? null);
            if ('' !== $rowId) {
                $rowsById[$rowId] = $row;
            }
        }

        if (!isset($rowsById[$normalizedId])) {
            return [];
        }

        $chain = [];
        $seen = [];
        $currentId = $normalizedId;
        while (isset($rowsById[$currentId])) {
            if (isset($seen[$currentId])) {
                throw new \RuntimeException('Category projection breadcrumb contains a parent cycle.');
            }

            $seen[$currentId] = true;
            $row = $rowsById[$currentId];
            array_unshift($chain, [
                'id' => $currentId,
                'name' => $this->nameValue($row),
                'slug' => $this->stringValue($row['slug'] ?? null),
            ]);

            $parentId = $this->stringValue($row['parent_id'] ?? null);
            if ('' === $parentId) {
                break;
            }

            $currentId = $parentId;
        }

        return $chain;
    }

    /**
     * @param list<array<string,mixed>>                                     $rows
     * @param array{parentId?:string,search?:string,first:int,after:string} $criteria
     *
     * @return list<array<string,mixed>>
     */
    private function filterRows(array $rows, array $criteria): array
    {
        $parentId = $criteria['parentId'] ?? null;
        $search = isset($criteria['search']) ? strtolower($criteria['search']) : null;

        return array_values(array_filter($rows, function (array $row) use ($parentId, $search): bool {
            if (null !== $parentId && $this->stringValue($row['parent_id'] ?? null) !== $parentId) {
                return false;
            }

            if (null === $search) {
                return true;
            }

            $haystack = strtolower($this->nameValue($row).' '.$this->stringValue($row['slug'] ?? null));

            return str_contains($haystack, $search);
        }));
    }

    /**
     * @param list<array<string,mixed>> $rows
     *
     * @return list<array<string,mixed>>
     */
    private function pageRows(array $rows, string $after): array
    {
        if ('' === $after) {
            return $rows;
        }

        $offset = 0;
        foreach ($rows as $index => $row) {
            if ($this->stringValue($row['id'] ?? null) === $after) {
                $offset = $index + 1;
                break;
            }
        }

        return array_slice($rows, $offset);
    }

    /**
     * @param array<string,mixed> $row
     *
     * @return array{id: string, name: string, slug: string, depth: int, path: string}
     */
    private function normalizeEdge(array $row): array
    {
        $path = $this->stringValue($row['path'] ?? null);

        return [
            'id' => $this->stringValue($row['id'] ?? null),
            'name' => $this->nameValue($row),
            'slug' => $this->stringValue($row['slug'] ?? null),
            'depth' => $this->depthFromPath($path),
            'path' => $path,
        ];
    }

    private function depthFromPath(string $path): int
    {
        $trimmed = trim($path, '/');

        return '' === $trimmed ? 0 : max(0, substr_count($trimmed, '/') + 1);
    }

    /** @param array<string,mixed> $row */
    private function nameValue(array $row): string
    {
        return $this->stringValue($row['name'] ?? $row['nameEntity'] ?? null);
    }

    private function stringValue(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }
}
