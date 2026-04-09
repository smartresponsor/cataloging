<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\RepositoryInterface\VirtualCategoryRepositoryInterface;
/**
 * Provides the catalog virtual collection service application service.
 */
final class CatalogVirtualCollectionService
{
    /**
     * Initializes the catalog virtual collection service service collaborators.
     */
    public function __construct(
        private readonly CatalogCollectionService $collectionService,
        private readonly VirtualCategoryRepositoryInterface $repository,
    ) {
    }

    /**
     * @param array<mixed> $rules
     *
     * @return list<array<string, list<bool|float|int|string>|bool|float|int|string|null>>
     */
    public function preview(array $rules): array
    {
        return $this->collectionService->build($this->normalizeRules($rules));
    }

    /**
     * @return array{
     *     id:string,
     *     name:string,
     *     rule:array<string,mixed>,
     *     data:list<array<string, list<bool|float|int|string>|bool|float|int|string|null>>,
     *     total:int,
     * }|null
     */
    public function apply(string $id): ?array
    {
        $virtualCategory = $this->repository->findById($id);
        if (null === $virtualCategory) {
            return null;
        }

        $data = $this->collectionService->build($this->normalizeRules($virtualCategory['rule']));

        return [
            'id' => $virtualCategory['id'],
            'name' => $virtualCategory['name'],
            'rule' => $virtualCategory['rule'],
            'data' => $data,
            'total' => count($data),
        ];
    }

    /**
     * @param array<mixed> $rules
     *
     * @return array<string, array<int, bool|float|int|string>|bool|float|int|string>
     */
    private function normalizeRules(array $rules): array
    {
        $normalized = [];
        foreach ($rules as $key => $value) {
            if (!is_string($key)) {
                continue;
            }
            if (is_bool($value) || is_float($value) || is_int($value) || is_string($value)) {
                $normalized[$key] = $value;
                continue;
            }
            if (!is_array($value)) {
                continue;
            }
            $items = [];
            foreach ($value as $item) {
                if (is_bool($item) || is_float($item) || is_int($item) || is_string($item)) {
                    $items[] = $item;
                }
            }
            $normalized[$key] = $items;
        }

        return $normalized;
    }
}
