<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 */

namespace App\Service;

final class CatalogCategoryMoveService implements CategoryMoveInterface
{
    private \PDO $pg;

    public function __construct(\PDO $pg)
    {
        $this->pg = $pg;
    }

    public function move(string $nodeId, string $newParentId, string $treeId, string $policy, bool $dryRun = false, ?string $locale = null): array
    {
        $this->pg->beginTransaction();

        try {
            $changed = 0;
            $redirects = [];

            if ($dryRun) {
                $this->pg->rollBack();
            } else {
                $this->pg->commit();
            }

            return [$changed, $redirects];
        } catch (\Throwable $e) {
            $this->pg->rollBack();

            throw new \RuntimeException('Move failed: '.$e->getMessage(), 0, $e);
        }
    }
}
