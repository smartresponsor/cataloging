<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\RepositoryInterface\Catalog\CatalogVirtualCategoryRepositoryInterface;

/**
 * Provides the catalog virtual collection service application service.
 */
final readonly class CatalogVirtualCollectionService
{
    /**
     * Initializes the catalog virtual collection service service collaborators.
     */
    public function __construct(
        private CatalogCollectionService $collectionService,
        private CatalogVirtualCategoryRepositoryInterface $repository,
        private CategoryCollectionRuleNormalizer $ruleNormalizer,
    ) {
    }

    /**
     * @param array<string,mixed> $rules
     *
     * @return list<array<string, list<bool|float|int|string>|bool|float|int|string|null>>
     */
    public function preview(array $rules): array
    {
        return $this->collectionService->build($this->ruleNormalizer->normalize($rules));
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

        $data = $this->collectionService->build($this->ruleNormalizer->normalize($virtualCategory['rule']));

        return [
            'id' => $virtualCategory['id'],
            'name' => $virtualCategory['name'],
            'rule' => $virtualCategory['rule'],
            'data' => $data,
            'total' => count($data),
        ];
    }
}
