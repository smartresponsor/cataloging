<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Api\Graphql;

use App\Entity\CategoryEntity;

final class CategoryNodeResolver
{
    public function __construct(private readonly CategoryRepository $repository)
    {
    }

    public function node(string $id): ?array
    {
        /** @var CategoryEntity|null $category */
        $category = $this->repository->find($id);

        return null === $category ? null : $this->map($category);
    }

    public function category(string $slug): ?array
    {
        /** @var CategoryEntity|null $category */
        $category = $this->repository->findOneBy(['slug' => $slug]);

        return null === $category ? null : $this->map($category);
    }

    private function map(CategoryEntity $category): array
    {
        return [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'slug' => $category->getSlug(),
            'path' => $category->getPath(),
            'depth' => $category->getDepth(),
        ];
    }
}
