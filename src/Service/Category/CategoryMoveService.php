/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 */
<?php
declare(strict_types=1);

namespace App\Service;

use PDO;
use RuntimeException;

final class CategoryMoveService implements CategoryMoveInterface
{
    private PDO $pg;

    public function __construct(PDO $pg)
    {
        $this->pg = $pg;
    }

    public function move(string $nodeId, string $newParentId, string $treeId, string $policy, bool $dryRun = false, ?string $locale = null): array
    {
        // NOTE: This is a reference implementation sketch.
        // The actual SQL uses LTREE prefix replacement in a single transaction.
        $this->pg->beginTransaction();
        try {
            // 1) Pre-checks (existence, same/different tree handling, cycle check via ltree)
            // 2) SELECT ... FOR UPDATE node, newParent and subtree
            // 3) Compute new base path (using stable segment or slug-derived)
            // 4) Update subtree paths and depth
            // 5) Recompute slug_path and collect redirects (old->new) per locale
            // 6) Emit events to outbox
            $changed = 0;
            $redirects = [];

            // Replace with your concrete SQL calls (fn_rebase_path, fn_collect_redirects)
            if ($dryRun) {
                $this->pg->rollBack();
            } else {
                $this->pg->commit();
            }

            return [$changed, $redirects];
        } catch (\Throwable $e) {
            $this->pg->rollBack();
            throw new RuntimeException('Move failed: '.$e->getMessage(), 0, $e);
        }
    }
}
