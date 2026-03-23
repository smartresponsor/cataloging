<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\CategoryMoveInterface;

final class CategoryMoveService implements CategoryMoveInterface
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
                if ($this->pg->inTransaction()) {
                    $this->pg->rollBack();
                }
            } else {
                $this->pg->commit();
            }

            return [$changed, $redirects];
        } catch (\PDOException|\RuntimeException $e) {
            error_log('[CategoryMoveService] '.$e->getMessage());

            if ($this->pg->inTransaction()) {
                $this->pg->rollBack();
            }

            throw new \RuntimeException('Move failed: '.$e->getMessage(), 0, $e);
        }
    }
}
