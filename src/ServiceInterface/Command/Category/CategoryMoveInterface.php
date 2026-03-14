<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\ServiceInterface\Command\Category;

final class CategoryMovePolicy
{
    public const PRESERVE_SLUG = 'preserveSlug';
    public const REBUILD_SLUG = 'rebuildSlug';

    private function __construct()
    {
    }
}

interface CategoryMoveInterface
{
    /**
     * Perform a transactional rebase of the node path under the new parent.
     *
     * @return array{0:int,1:list<array<string,mixed>>}
     */
    public function move(
        string $nodeId,
        string $newParentId,
        string $treeId,
        string $policy,
        bool $dryRun = false,
        ?string $locale = null,
    ): array;
}
