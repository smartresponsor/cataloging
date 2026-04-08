<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;
/**
 * Defines the contract for catalog move service.
 */
interface CatalogMoveServiceInterface
{
    /**
     * Perform a transactional rebase of the node path under the new parent.
     *
     * @return array{0:int,1:array<int,mixed>}
     */
    public function move(string $nodeId, string $newParentId, string $treeId, string $policy, bool $dryRun = false, ?string $locale = null): array;
}
