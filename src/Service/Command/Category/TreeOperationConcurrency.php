<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service\Command\Category;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class TreeOperationConcurrency
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly \PDO $pdo,
        private readonly TreeLock $lock,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function move(string $nodeId, ?string $newParentId): void
    {
        if ($nodeId === $newParentId) {
            throw new \InvalidArgumentException('A category cannot become its own parent.');
        }

        $this->lock->acquire('category_tree');
        try {
            $this->pdo->beginTransaction();

            $statement = $this->pdo->prepare('SELECT path FROM category_entity WHERE id = :id FOR UPDATE');
            $statement->bindValue(':id', $nodeId);
            $statement->execute();
            $rowNode = $statement->fetch(\PDO::FETCH_ASSOC);
            $path = (string) ($rowNode['path'] ?? '');

            if (null !== $newParentId) {
                $parentStatement = $this->pdo->prepare('SELECT path FROM category_entity WHERE id = :pid FOR UPDATE');
                $parentStatement->bindValue(':pid', $newParentId);
                $parentStatement->execute();
                $rowParent = $parentStatement->fetch(\PDO::FETCH_ASSOC);
                $parentPath = (string) ($rowParent['path'] ?? '');

                if ('' !== $parentPath && str_starts_with($parentPath, $path)) {
                    throw new \InvalidArgumentException('The category cannot be moved under its own subtree.');
                }
            }

            $this->pdo->commit();
        } catch (\Throwable $throwable) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            $this->logger->error('Category tree operation failed.', [
                'nodeId' => $nodeId,
                'newParentId' => $newParentId,
                'exception' => $throwable,
            ]);

            throw $throwable;
        } finally {
            $this->lock->release('category_tree');
        }
    }
}
