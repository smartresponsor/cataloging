<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

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

            $p = $this->pdo->prepare('SELECT path FROM category_entity WHERE id = :id FOR UPDATE');
            if (false === $p) {
                throw new \RuntimeException('Failed to prepare node-path query');
            }
            $p->bindValue(':id', $nodeId);
            $p->execute();
            $rowNode = $p->fetch(\PDO::FETCH_ASSOC);
            $path = (string) ($rowNode['path'] ?? '');

            if (null !== $newParentId) {
                $pp = $this->pdo->prepare('SELECT path FROM category_entity WHERE id = :pid FOR UPDATE');
                if (false === $pp) {
                    throw new \RuntimeException('Failed to prepare parent-path query');
                }
                $pp->bindValue(':pid', $newParentId);
                $pp->execute();
                $rowParent = $pp->fetch(\PDO::FETCH_ASSOC);
                $pPath = (string) ($rowParent['path'] ?? '');
                if ('' !== $pPath && '' !== $path && str_starts_with($pPath, $path)) {
                    throw new \InvalidArgumentException('Cycle detected');
                }
            }

            $this->pdo->commit();
        } catch (\PDOException $e) {
            error_log('[TreeOperationConcurrency] '.$e->getMessage());

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw new \RuntimeException('Tree move failed: '.$e->getMessage(), 0, $e);
        } finally {
            $this->lock->release('category_tree');
        }
    }
}
