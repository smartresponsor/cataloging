<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 */

namespace App\ServiceInterface;

interface CategoryMoveInterface
{
    /**
     * Perform a transactional rebase of the node path under the new parent.
     * Return tuple: [changedCount, redirects].
     */
    public function move(string $nodeId, string $newParentId, string $treeId, string $policy, bool $dryRun = false, ?string $locale = null): array;
}
