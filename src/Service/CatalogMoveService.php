<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ServiceInterface\CategoryMoveInterface;
use App\Cataloging\ValueObject\CatalogMoveRequest;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Provides the catalog move service application service.
 */
final readonly class CatalogMoveService implements CategoryMoveInterface
{
    /**
     * Initializes the catalog move service service collaborators.
     */
    public function __construct(
        private ManagerRegistry $registry,
        private string $connectionName = 'data',
    ) {
    }

    /**
     * Handles the move workflow.
     */
    public function move(CatalogMoveRequest $request): array
    {
        $normalizedNodeId = trim($request->nodeId());
        $normalizedNewParentId = trim($request->newParentId());
        $normalizedTreeId = trim($request->treeId());
        $normalizedPolicy = trim($request->policy());
        $dryRun = $request->dryRun();
        $locale = $request->locale();
        $normalizedLocale = is_string($locale) ? trim($locale) : null;

        if ('' === $normalizedNodeId) {
            throw new \InvalidArgumentException('nodeId is required');
        }
        if ('' === $normalizedNewParentId) {
            throw new \InvalidArgumentException('newParentId is required');
        }
        if ('' === $normalizedTreeId) {
            throw new \InvalidArgumentException('treeId is required');
        }
        if ('' === $normalizedPolicy) {
            throw new \InvalidArgumentException('policy is required');
        }
        if ($normalizedNodeId === $normalizedNewParentId) {
            throw new \InvalidArgumentException('A node cannot be moved under itself.');
        }
        unset($normalizedLocale);

        $connection = $this->connection();

        $connection->beginTransaction();

        try {
            $node = $this->fetchNode($normalizedNodeId);
            if (null === $node) {
                throw new \RuntimeException(sprintf('Category node "%s" was not found.', $normalizedNodeId));
            }
            $newParent = $this->fetchNode($normalizedNewParentId);
            if (null === $newParent) {
                throw new \RuntimeException(sprintf('New parent "%s" was not found.', $normalizedNewParentId));
            }

            $oldPath = $this->pathValue($node['path'] ?? null);
            $newParentPath = $this->pathValue($newParent['path'] ?? null);

            if ($newParentPath === $oldPath || str_starts_with($newParentPath, $oldPath.'.')) {
                throw new \InvalidArgumentException('Cannot move a node under its own descendant.');
            }

            $oldParentPath = $this->parentPath($oldPath);
            if ($oldParentPath === $newParentPath) {
                if ($connection->isTransactionActive()) {
                    $connection->rollBack();
                }

                return [0, []];
            }

            $leafSegment = $this->lastSegment($oldPath);
            $newPath = '' !== $newParentPath ? $newParentPath.'.'.$leafSegment : $leafSegment;
            $subtree = $this->fetchSubtree($oldPath);

            $changed = 0;
            $redirects = [];
            foreach ($subtree as $row) {
                $currentPath = $this->pathValue($row['path'] ?? null);
                $rebasedPath = $this->rebasePath($currentPath, $oldPath, $newPath);
                if ($rebasedPath === $currentPath) {
                    continue;
                }

                $this->updateRow($this->idValue($row['id'] ?? null), $rebasedPath, $this->depthFromPath($rebasedPath));
                ++$changed;
                $redirects[] = [
                    'id' => $this->idValue($row['id'] ?? null),
                    'from' => $currentPath,
                    'to' => $rebasedPath,
                ];
            }

            if ($dryRun && $connection->isTransactionActive()) {
                $connection->rollBack();
            }
            if (!$dryRun) {
                $connection->commit();
            }

            return [$changed, $redirects];
        } catch (\Throwable $exception) {
            error_log('[CatalogMoveService] '.$exception->getMessage());

            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }

            if ($exception instanceof \InvalidArgumentException || $exception instanceof \RuntimeException) {
                throw $exception;
            }

            throw new \RuntimeException('Move failed: '.$exception->getMessage(), 0, $exception);
        }
    }

    /** @return array<string, mixed>|null */
    private function fetchNode(string $id): ?array
    {
        $statement = $this->connection()->prepare('SELECT id, slug, path, depth FROM category WHERE id = :id LIMIT 1');
        $statement->bindValue(':id', $id);
        $result = $statement->executeQuery();
        $row = $result->fetchAssociative();

        return false === $row ? null : $row;
    }

    /** @return list<array<string, mixed>> */
    private function fetchSubtree(string $path): array
    {
        $statement = $this->connection()->prepare(
            'SELECT id, path, depth
             FROM category
             WHERE CAST(path AS TEXT) = :path OR CAST(path AS TEXT) LIKE :prefix
             ORDER BY depth ASC, id ASC'
        );
        $statement->bindValue(':path', $path);
        $statement->bindValue(':prefix', $path.'.%');
        $result = $statement->executeQuery();

        return $result->fetchAllAssociative();
    }

    private function updateRow(string $id, string $path, int $depth): void
    {
        $statement = $this->connection()->prepare('UPDATE category SET path = :path, depth = :depth WHERE id = :id');
        $statement->bindValue(':path', $path);
        $statement->bindValue(':depth', $depth);
        $statement->bindValue(':id', $id);
        $statement->executeStatement();
    }

    private function connection(): Connection
    {
        $candidates = array_values(array_unique(array_filter([
            trim($this->connectionName),
            'data',
            'app_data',
            'user_data',
            null,
        ], static fn (mixed $name): bool => is_string($name) && '' !== $name)));

        foreach ($candidates as $candidate) {
            try {
                /** @var Connection $connection */
                $connection = $this->registry->getConnection($candidate);

                return $connection;
            } catch (\Throwable) {
            }
        }

        /** @var Connection $connection */
        $connection = $this->registry->getConnection();

        return $connection;
    }

    private function parentPath(string $path): ?string
    {
        $position = strrpos($path, '.');
        if (false === $position) {
            return null;
        }

        return substr($path, 0, $position);
    }

    private function lastSegment(string $path): string
    {
        $segments = explode('.', $path);
        $segment = end($segments);

        return (string) $segment;
    }

    private function rebasePath(string $path, string $oldPrefix, string $newPrefix): string
    {
        if ($path === $oldPrefix) {
            return $newPrefix;
        }
        if (!str_starts_with($path, $oldPrefix.'.')) {
            return $path;
        }

        return $newPrefix.substr($path, strlen($oldPrefix));
    }

    private function depthFromPath(string $path): int
    {
        if ('' === $path) {
            return 0;
        }

        return substr_count($path, '.');
    }

    private function pathValue(mixed $path): string
    {
        if (!is_scalar($path)) {
            throw new \RuntimeException('Category path must be a scalar value.');
        }

        $normalized = trim((string) $path);
        if ('' === $normalized) {
            throw new \RuntimeException('Category path cannot be empty.');
        }

        return $normalized;
    }

    private function idValue(mixed $id): string
    {
        if (!is_scalar($id)) {
            throw new \RuntimeException('Category id must be a scalar value.');
        }

        $normalized = trim((string) $id);
        if ('' === $normalized) {
            throw new \RuntimeException('Category id cannot be empty.');
        }

        return $normalized;
    }
}
