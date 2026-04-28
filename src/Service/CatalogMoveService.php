<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use App\Cataloging\ServiceInterface\CategoryMoveInterface;
use App\Cataloging\ValueObject\CatalogMoveRequest;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Provides the catalog move service application service.
 */
final readonly class CatalogMoveService implements CategoryMoveInterface
{
    /**
     * Initializes the catalog move service service collaborators.
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
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

        $this->entityManager->beginTransaction();

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
                $this->entityManager->rollback();

                return [0, []];
            }

            $leafSegment = $this->lastSegment($oldPath);
            $newPath = '' !== $newParentPath ? $newParentPath.'.'.$leafSegment : $leafSegment;
            $subtree = $this->fetchSubtree($oldPath);

            $changed = 0;
            $redirects = [];
            foreach ($subtree as $row) {
                $currentPath = $row['path'];
                $rebasedPath = $this->rebasePath($currentPath, $oldPath, $newPath);
                if ($rebasedPath === $currentPath) {
                    continue;
                }

                $this->updateRow($row['id'], $rebasedPath, $this->depthFromPath($rebasedPath));
                ++$changed;
                $redirects[] = [
                    'id' => $row['id'],
                    'from' => $currentPath,
                    'to' => $rebasedPath,
                ];
            }

            if ($dryRun) {
                $this->entityManager->rollback();

                return [$changed, $redirects];
            }

            $this->entityManager->flush();
            $this->entityManager->commit();

            return [$changed, $redirects];
        } catch (\Throwable $exception) {
            error_log('[CatalogMoveService] '.$exception->getMessage());

            $this->entityManager->rollback();

            if ($exception instanceof \InvalidArgumentException || $exception instanceof \RuntimeException) {
                throw $exception;
            }

            throw new \RuntimeException('Move failed: '.$exception->getMessage(), 0, $exception);
        }
    }

    /** @return array<string, mixed>|null */
    private function fetchNode(string $id): ?array
    {
        $entity = $this->entityManager->getRepository(CatalogCategoryEntity::class)->find($id);
        if (!$entity instanceof CatalogCategoryEntity) {
            return null;
        }

        return [
            'id' => $entity->getId(),
            'slug' => $entity->getSlug(),
            'path' => $entity->getPath(),
            'depth' => $entity->getDepth(),
        ];
    }

    /** @return list<array{id:string,path:string,depth:int}> */
    private function fetchSubtree(string $path): array
    {
        $rows = $this->entityManager->createQuery(
            'SELECT c.id AS id, c.path AS path, c.depth AS depth
             FROM App\Cataloging\Entity\Catalog\CatalogCategoryEntity c
             WHERE c.path = :path OR c.path LIKE :prefix
             ORDER BY c.depth ASC, c.id ASC'
        )->setParameter('path', $path)
         ->setParameter('prefix', $path.'.%')
         ->getArrayResult();

        $result = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $result[] = $this->normalizeSubtreeRow($row);
        }

        return $result;
    }

    /**
     * @param array<array-key, mixed> $row
     *
     * @return array{id:string,path:string,depth:int}
     */
    private function normalizeSubtreeRow(array $row): array
    {
        $path = $this->pathValue($row['path']);

        return [
            'id' => $this->idValue($row['id']),
            'path' => $path,
            'depth' => $this->depthFromPath($path),
        ];
    }

    private function updateRow(string $id, string $path, int $depth): void
    {
        $entity = $this->entityManager->getRepository(CatalogCategoryEntity::class)->find($id);
        if (!$entity instanceof CatalogCategoryEntity) {
            throw new \RuntimeException(sprintf('Category node "%s" was not found for ORM update.', $id));
        }

        $entity->setPath($path);
        $entity->setDepth($depth);
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
