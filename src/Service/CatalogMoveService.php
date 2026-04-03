<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\CategoryMoveInterface;

final class CatalogMoveService implements CategoryMoveInterface
{
    public function __construct(private readonly \PDO $pg)
    {
    }

    public function move(string $nodeId, string $newParentId, string $treeId, string $policy, bool $dryRun = false, ?string $locale = null): array
    {
        $normalizedNodeId = trim($nodeId);
        $normalizedNewParentId = trim($newParentId);
        $normalizedTreeId = trim($treeId);
        $normalizedPolicy = trim($policy);
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

        $this->pg->beginTransaction();

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
                if ($this->pg->inTransaction()) {
                    $this->pg->rollBack();
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

            if ($dryRun && $this->pg->inTransaction()) {
                $this->pg->rollBack();
            }
            if (!$dryRun) {
                $this->pg->commit();
            }

            return [$changed, $redirects];
        } catch (\Throwable $e) {
            error_log('[CatalogMoveService] '.$e->getMessage());

            if ($this->pg->inTransaction()) {
                $this->pg->rollBack();
            }

            if ($e instanceof \InvalidArgumentException || $e instanceof \RuntimeException) {
                throw $e;
            }

            throw new \RuntimeException('Move failed: '.$e->getMessage(), 0, $e);
        }
    }

    /** @return array<string, mixed>|null */
    private function fetchNode(string $id): ?array
    {
        $statement = $this->pg->prepare('SELECT id, slug, path, depth FROM category WHERE id = :id LIMIT 1');
        $statement->bindValue(':id', $id, \PDO::PARAM_STR);
        $statement->execute();
        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        return is_array($row) ? $row : null;
    }

    /** @return list<array<string, mixed>> */
    private function fetchSubtree(string $path): array
    {
        $statement = $this->pg->prepare(
            'SELECT id, path, depth
             FROM category
             WHERE CAST(path AS TEXT) = :path OR CAST(path AS TEXT) LIKE :prefix
             ORDER BY depth ASC, id ASC'
        );
        $statement->bindValue(':path', $path, \PDO::PARAM_STR);
        $statement->bindValue(':prefix', $path.'.%', \PDO::PARAM_STR);
        $statement->execute();

        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return array_values(array_filter($rows, 'is_array'));
    }

    private function updateRow(string $id, string $path, int $depth): void
    {
        $statement = $this->pg->prepare('UPDATE category SET path = :path, depth = :depth WHERE id = :id');
        $statement->bindValue(':path', $path, \PDO::PARAM_STR);
        $statement->bindValue(':depth', $depth, \PDO::PARAM_INT);
        $statement->bindValue(':id', $id, \PDO::PARAM_STR);
        $statement->execute();
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
