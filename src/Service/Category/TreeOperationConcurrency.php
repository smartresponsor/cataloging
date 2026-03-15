<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Layer\Category;

final class TreeOperationConcurrency
{
    private \PDO $pdo;
    private TreeLock $lock;

    public function __construct(\PDO $pdo, TreeLock $lock)
    {
        $this->pdo = $pdo;
        $this->lock = $lock;
    }

    public function move(string $nodeId, ?string $newParentId): void
    {
        if ($nodeId === $newParentId) {
            throw new \InvalidArgumentException('Node cannot be parent of itself');
        }

        $this->lock->acquire('category_tree');
        try {
            $this->pdo->beginTransaction();

            // Cycle detection (simplified): prevent moving under its own subtree path.
            $p = $this->pdo->prepare('SELECT path FROM category_entity WHERE id = :id FOR UPDATE');
            $p->bindValue(':id', $nodeId);
            $p->execute();
            $rowNode = $p->fetch(\PDO::FETCH_ASSOC);
            $path = (string) ($rowNode['path'] ?? '');

            if (null !== $newParentId) {
                $pp = $this->pdo->prepare('SELECT path FROM category_entity WHERE id = :pid FOR UPDATE');
                $pp->bindValue(':pid', $newParentId);
                $pp->execute();
                $rowParent = $pp->fetch(\PDO::FETCH_ASSOC);
                $pPath = (string) ($rowParent['path'] ?? '');
                if ('' !== $pPath && str_starts_with($pPath, $path)) {
                    throw new \InvalidArgumentException('Cycle detected');
                }
            }

            // Apply move — application layer should update parent_id and path.
            // ...

            $this->pdo->commit();
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        } finally {
            $this->lock->release('category_tree');
        }
    }
}
