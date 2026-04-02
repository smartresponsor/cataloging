<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\RepositoryInterface\VirtualCategoryRepositoryInterface;

final class CatalogVirtualCategoryService
{
    public function __construct(
        private readonly CatalogCollectionService $collectionService,
        private readonly VirtualCategoryRepositoryInterface $repository,
        private readonly RuleNormalizer $ruleNormalizer,
    ) {
    }

    /** @param array<mixed> $rules */
    public function preview(array $rules): array
    {
        return $this->collectionService->build($this->ruleNormalizer->normalize($rules));
    }

    public function apply(string $id): ?array
    {
        $virtualCategory = $this->repository->findById($id);
        if (null === $virtualCategory) {
            return null;
        }

        $data = $this->collectionService->build(
            $this->ruleNormalizer->normalize($virtualCategory['rule'])
        );

        return [
            'id' => $virtualCategory['id'],
            'name' => $virtualCategory['name'],
            'rule' => $virtualCategory['rule'],
            'data' => $data,
            'total' => count($data),
        ];
    }
}
