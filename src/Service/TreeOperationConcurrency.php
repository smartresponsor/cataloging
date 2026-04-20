<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the tree operation concurrency application service.
 */
final class TreeOperationConcurrency
{
    private \PDO $databaseConnection;
    private TreeLock $treeLock;

    /**
     * Initializes the tree operation concurrency service collaborators.
     */
    public function __construct(\PDO $databaseConnection, TreeLock $treeLock)
    {
        $this->databaseConnection = $databaseConnection;
        $this->treeLock = $treeLock;
    }

    /**
     * Handles the move workflow.
     */
    public function move(string $nodeId, ?string $newParentId): void
    {
        if ($nodeId === $newParentId) {
            throw new \InvalidArgumentException('Node cannot be parent of itself');
        }

        $this->treeLock->acquire('category_tree');
        try {
            $this->databaseConnection->beginTransaction();

            $nodePathStatement = $this->databaseConnection->prepare('SELECT path FROM category_entity WHERE id = :id FOR UPDATE');
            if (false === $nodePathStatement) {
                throw new \RuntimeException('Failed to prepare node-path query');
            }
            $nodePathStatement->bindValue(':id', $nodeId);
            $nodePathStatement->execute();
            $rowNode = $nodePathStatement->fetch(\PDO::FETCH_ASSOC);
            $nodePath = $this->pathFromFetch($rowNode);

            if (null !== $newParentId) {
                $parentPathStatement = $this->databaseConnection->prepare('SELECT path FROM category_entity WHERE id = :parentId FOR UPDATE');
                if (false === $parentPathStatement) {
                    throw new \RuntimeException('Failed to prepare parent-path query');
                }
                $parentPathStatement->bindValue(':parentId', $newParentId);
                $parentPathStatement->execute();
                $rowParent = $parentPathStatement->fetch(\PDO::FETCH_ASSOC);
                $parentPath = $this->pathFromFetch($rowParent);
                if ('' !== $parentPath && '' !== $nodePath && str_starts_with($parentPath, $nodePath)) {
                    throw new \InvalidArgumentException('Cycle detected');
                }
            }

            $this->databaseConnection->commit();
        } catch (\Throwable $exception) {
            error_log('[TreeOperationConcurrency] '.$exception->getMessage());

            if ($this->databaseConnection->inTransaction()) {
                $this->databaseConnection->rollBack();
            }

            if ($exception instanceof \InvalidArgumentException || $exception instanceof \RuntimeException) {
                throw $exception;
            }

            throw new \RuntimeException('Tree move failed: '.$exception->getMessage(), 0, $exception);
        } finally {
            $this->treeLock->release('category_tree');
        }
    }

    private function pathFromFetch(mixed $row): string
    {
        if (!is_array($row)) {
            return '';
        }

        $path = $row['path'] ?? '';

        return is_string($path) ? $path : '';
    }
}
