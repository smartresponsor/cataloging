<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service\Category;

use App\Cataloging\ServiceInterface\CategoryReadRepositoryInterface;
use App\Cataloging\ValueObject\CategoryReadRepositoryListRequest;

/**
 * Provides the category category service application service.
 */
final class CategoryCategoryService
{
    private CategoryReadRepositoryInterface $repo;

    /**
     * Initializes the category category service service collaborators.
     */
    public function __construct(CategoryReadRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @return array{
     *     edges: array<int, array{id: string, name: string, slug: string, depth: int, path: string}>,
     *     pageInfo: array{endCursor?: string, hasNextPage: bool},
     *     total?: int,
     *     approxTotal?: int,
     * }
     */
    public function list(?CategoryReadRepositoryListRequest $request = null): array
    {
        $normalizedRequest = $request ?? new CategoryReadRepositoryListRequest();
        if ($normalizedRequest->withTotal() && $normalizedRequest->approxTotal()) {
            throw new \InvalidArgumentException('Choose either withTotal=true or approxTotal=true, not both.');
        }

        return $this->repo->list($normalizedRequest);
    }

    /**
     * @return array<int, array{id: string, name: string, slug: string}>
     */
    public function breadcrumb(string $id): array
    {
        return $this->repo->breadcrumb($id);
    }
}
