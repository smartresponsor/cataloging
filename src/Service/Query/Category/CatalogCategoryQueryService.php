<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Service\Query\Category;

final class CatalogCategoryQueryService implements CategoryQueryServiceInterface
{
    public function __construct(private readonly CategoryRepositoryInterface $repo)
    {
    }

    public function list(array $opt = []): array
    {
        $withTotal = (bool) ($opt['withTotal'] ?? false);
        $approxTotal = (bool) ($opt['approxTotal'] ?? false);
        if ($withTotal && $approxTotal) {
            throw new \InvalidArgumentException('Choose either withTotal=true or approxTotal=true, not both.');
        }

        return $this->repo->list($opt, $withTotal, $approxTotal);
    }

    public function breadcrumb(string $id): array
    {
        return $this->repo->breadcrumb($id);
    }
}
