<?php

declare(strict_types=1);

namespace App\Service;

use App\PolicyInterface\CategoryWorkflowPolicyInterface;
use App\ServiceInterface\CatalogPublicationGateServiceInterface;
use App\ServiceInterface\CategoryMutationServiceInterface;
use App\ValueObject\CategoryWorkflowState;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Symfony\Component\Uid\Uuid;

final class CategoryMutationService implements CategoryMutationServiceInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly OutboxWriter $outboxWriter,
        private readonly CacheInvalidationRecorder $cacheInvalidationRecorder,
        private readonly CatalogPublicationGateServiceInterface $publicationGateService,
        private readonly CategoryWorkflowPolicyInterface $workflowPolicy,
    ) {
    }

    public function move(string $categoryId, string $newParentId, string $actorId, string $treeId = 'catalog', string $policy = 'strict', bool $dryRun = false, ?string $locale = null): array
    {
        $normalizedCategoryId = $this->requiredString($categoryId, 'categoryId');
        $normalizedNewParentId = $this->requiredString($newParentId, 'newParentId');
        $normalizedActorId = $this->requiredString($actorId, 'actorId');
        $normalizedTreeId = $this->requiredString($treeId, 'treeId');
        $normalizedPolicy = $this->requiredString($policy, 'policy');
        $normalizedLocale = null !== $locale ? trim($locale) : null;
        unset($normalizedLocale);

        if ($normalizedCategoryId === $normalizedNewParentId) {
            throw new \InvalidArgumentException('A node cannot be moved under itself.');
        }

        /** @var array{id:string,oldParentId:?string,newParentId:string,treeId:string,policy:string,changedCount:int,dryRun:bool,redirects:list<array{id:string,from:string,to:string}>} $result */
        $result = $this->connection->transactional(function (Connection $connection) use ($normalizedActorId, $normalizedCategoryId, $normalizedNewParentId, $normalizedPolicy, $normalizedTreeId, $dryRun): array {
            $node = $this->fetchCategory($connection, $normalizedCategoryId);
            $newParent = $this->fetchCategory($connection, $normalizedNewParentId);

            $oldPath = $this->requiredPath($node['path'] ?? null);
            $newParentPath = $this->requiredPath($newParent['path'] ?? null);

            if ($newParentPath === $oldPath || str_starts_with($newParentPath, $oldPath.'.')) {
                throw new \InvalidArgumentException('Cannot move a node under its own descendant.');
            }

            $oldParentId = $this->nullableScalarToString($node['parent_id'] ?? null);
            if ($oldParentId === $normalizedNewParentId) {
                return [
                    'id' => $normalizedCategoryId,
                    'oldParentId' => $oldParentId,
                    'newParentId' => $normalizedNewParentId,
                    'treeId' => $normalizedTreeId,
                    'policy' => $normalizedPolicy,
                    'changedCount' => 0,
                    'dryRun' => $dryRun,
                    'redirects' => [],
                ];
            }

            $leafSegment = $this->lastSegment($oldPath);
            $newPath = '' !== $newParentPath ? $newParentPath.'.'.$leafSegment : $leafSegment;
            $subtree = $this->fetchSubtree($connection, $oldPath);

            $changedCount = 0;
            $redirects = [];
            foreach ($subtree as $row) {
                $currentPath = $this->requiredPath($row['path'] ?? null);
                $rebasedPath = $this->rebasePath($currentPath, $oldPath, $newPath);
                if ($rebasedPath === $currentPath) {
                    continue;
                }

                $rowId = $this->requiredString($row['id'] ?? null, 'category row id');
                $level = $this->levelFromPath($rebasedPath);
                $parentId = $rowId === $normalizedCategoryId
                    ? $normalizedNewParentId
                    : $this->nullableScalarToString($row['parent_id'] ?? null);

                $connection->update(
                    'category',
                    [
                        'path' => $rebasedPath,
                        'level' => $level,
                        'parent_id' => $parentId,
                    ],
                    ['id' => $rowId],
                    [
                        'path' => ParameterType::STRING,
                        'level' => ParameterType::INTEGER,
                        'parent_id' => null === $parentId ? ParameterType::NULL : ParameterType::STRING,
                        'id' => ParameterType::STRING,
                    ],
                );

                ++$changedCount;
                $redirects[] = ['id' => $rowId, 'from' => $currentPath, 'to' => $rebasedPath];
            }

            if ($dryRun) {
                return [
                    'id' => $normalizedCategoryId,
                    'oldParentId' => $oldParentId,
                    'newParentId' => $normalizedNewParentId,
                    'treeId' => $normalizedTreeId,
                    'policy' => $normalizedPolicy,
                    'changedCount' => $changedCount,
                    'dryRun' => true,
                    'redirects' => $redirects,
                ];
            }

            $this->writeAudit($connection, 'category.move', [
                'categoryId' => $normalizedCategoryId,
                'actorId' => $normalizedActorId,
                'oldParentId' => $oldParentId,
                'newParentId' => $normalizedNewParentId,
                'treeId' => $normalizedTreeId,
                'policy' => $normalizedPolicy,
                'changedCount' => $changedCount,
                'redirects' => $redirects,
            ]);
            $this->outboxWriter->append('category.moved', [
                'categoryId' => $normalizedCategoryId,
                'actorId' => $normalizedActorId,
                'oldParentId' => $oldParentId,
                'newParentId' => $normalizedNewParentId,
                'treeId' => $normalizedTreeId,
                'policy' => $normalizedPolicy,
                'changedCount' => $changedCount,
                'redirects' => $redirects,
            ], sprintf('category.move:%s:%s:%s', $normalizedCategoryId, $normalizedNewParentId, sha1(json_encode($redirects, JSON_THROW_ON_ERROR))));

            return [
                'id' => $normalizedCategoryId,
                'oldParentId' => $oldParentId,
                'newParentId' => $normalizedNewParentId,
                'treeId' => $normalizedTreeId,
                'policy' => $normalizedPolicy,
                'changedCount' => $changedCount,
                'dryRun' => false,
                'redirects' => $redirects,
            ];
        });

        if (!$result['dryRun']) {
            $this->cacheInvalidationRecorder->invalidate($result['id']);
        }

        return $result;
    }

    public function publish(string $categoryId, bool $published, array $checks, string $actorId, string $reason): array
    {
        $normalizedCategoryId = $this->requiredString($categoryId, 'categoryId');
        $normalizedActorId = $this->requiredString($actorId, 'actorId');
        $normalizedReason = $this->requiredString($reason, 'reason');
        $normalizedChecks = $this->normalizeChecks($checks);

        /** @var array{id:string,published:bool,workflowState:string,previousWorkflowState:string,blockers:list<string>,warnings:list<string>,checks:array<string,bool>,publishedAt:?string,reason:string} $result */
        $result = $this->connection->transactional(function (Connection $connection) use ($normalizedCategoryId, $published, $normalizedChecks, $normalizedActorId, $normalizedReason): array {
            $category = $this->fetchCategory($connection, $normalizedCategoryId);
            $currentWorkflowState = $this->workflowStateValue($category['workflow_state'] ?? null);
            $previousPublished = (bool) ($category['published'] ?? false);

            if ($published) {
                $gate = $this->publicationGateService->evaluate($normalizedCategoryId, $currentWorkflowState, $normalizedChecks, $normalizedActorId, $normalizedReason);
                $payload = $gate->payload();
                if (($payload['publishable'] ?? false) !== true) {
                    throw new \DomainException('Category publication gate failed: '.implode(',', $this->stringList(is_iterable($payload['blockers'] ?? null) ? $payload['blockers'] : [])));
                }
                $targetState = CategoryWorkflowState::PUBLISHED;
                $publishedAt = (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s');
                $blockers = $this->stringList(is_iterable($payload['blockers'] ?? null) ? $payload['blockers'] : []);
                $warnings = $this->stringList(is_iterable($payload['warnings'] ?? null) ? $payload['warnings'] : []);
                $checksForResponse = $this->boolMap(is_iterable($payload['checks'] ?? null) ? $payload['checks'] : []);
            } else {
                $targetState = CategoryWorkflowState::DRAFT;
                $publishedAt = null;
                $blockers = [];
                $warnings = [];
                $checksForResponse = [];
            }

            $from = CategoryWorkflowState::fromString($currentWorkflowState);
            $to = CategoryWorkflowState::fromString($targetState);
            $this->workflowPolicy->assertTransitionAllowed($from, $to, $normalizedActorId, $normalizedReason);

            $connection->update(
                'category',
                [
                    'workflow_state' => $targetState,
                    'published' => $published,
                    'published_at' => $publishedAt,
                ],
                ['id' => $normalizedCategoryId],
                [
                    'workflow_state' => ParameterType::STRING,
                    'published' => ParameterType::BOOLEAN,
                    'published_at' => null === $publishedAt ? ParameterType::NULL : ParameterType::STRING,
                    'id' => ParameterType::STRING,
                ],
            );

            $payload = [
                'categoryId' => $normalizedCategoryId,
                'actorId' => $normalizedActorId,
                'reason' => $normalizedReason,
                'published' => $published,
                'previousPublished' => $previousPublished,
                'workflowState' => $targetState,
                'previousWorkflowState' => $currentWorkflowState,
                'checks' => $checksForResponse,
                'blockers' => $blockers,
                'warnings' => $warnings,
                'publishedAt' => $publishedAt,
            ];

            $this->writeAudit($connection, 'category.publish', $payload);
            $this->outboxWriter->append($published ? 'category.published' : 'category.unpublished', $payload, sprintf('category.publish:%s:%s:%s', $normalizedCategoryId, $published ? '1' : '0', sha1($normalizedReason)));

            return [
                'id' => $normalizedCategoryId,
                'published' => $published,
                'workflowState' => $targetState,
                'previousWorkflowState' => $currentWorkflowState,
                'blockers' => $blockers,
                'warnings' => $warnings,
                'checks' => $checksForResponse,
                'publishedAt' => $publishedAt,
                'reason' => $normalizedReason,
            ];
        });

        $this->cacheInvalidationRecorder->invalidate($result['id']);

        return $result;
    }

    /** @return array<string,mixed> */
    private function fetchCategory(Connection $connection, string $categoryId): array
    {
        $row = $connection->fetchAssociative(
            'SELECT id, parent_id, path, level, workflow_state, published, published_at FROM category WHERE id = :id LIMIT 1',
            ['id' => $categoryId],
            ['id' => ParameterType::STRING],
        );

        if (!is_array($row)) {
            throw new \RuntimeException(sprintf('Category "%s" was not found.', $categoryId));
        }

        return $row;
    }

    /** @return list<array<string,mixed>> */
    private function fetchSubtree(Connection $connection, string $path): array
    {
        $rows = $connection->fetchAllAssociative(
            'SELECT id, parent_id, path, level FROM category WHERE path = :path OR path LIKE :prefix ORDER BY level ASC, id ASC',
            ['path' => $path, 'prefix' => $path.'.%'],
            ['path' => ParameterType::STRING, 'prefix' => ParameterType::STRING],
        );

        return array_values(array_filter($rows, 'is_array'));
    }

    /** @param array<string,mixed> $payload */
    private function writeAudit(Connection $connection, string $action, array $payload): void
    {
        $connection->insert('category_audit', [
            'id' => Uuid::v7()->toRfc4122(),
            'action' => $action,
            'payload' => json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
            'created_at' => (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
        ], [
            'id' => ParameterType::STRING,
            'action' => ParameterType::STRING,
            'payload' => ParameterType::STRING,
            'created_at' => ParameterType::STRING,
        ]);
    }

    /**
     * @param array<string,bool> $checks
     *
     * @return array<string,bool>
     */
    private function normalizeChecks(array $checks): array
    {
        $normalized = [];
        foreach ($checks as $name => $value) {
            if (!is_string($name)) {
                continue;
            }
            $normalized[$name] = (bool) $value;
        }

        return $normalized;
    }

    /**
     * @param iterable<mixed> $values
     *
     * @return list<string>
     */
    private function stringList(iterable $values): array
    {
        $normalized = [];
        foreach ($values as $value) {
            if (!is_scalar($value)) {
                continue;
            }
            $trimmed = trim((string) $value);
            if ('' === $trimmed) {
                continue;
            }
            $normalized[] = $trimmed;
        }

        return array_values($normalized);
    }

    /**
     * @param iterable<mixed> $values
     *
     * @return array<string,bool>
     */
    private function boolMap(iterable $values): array
    {
        $normalized = [];
        foreach ($values as $key => $value) {
            if (!is_string($key)) {
                continue;
            }
            $normalized[$key] = (bool) $value;
        }

        return $normalized;
    }

    private function workflowStateValue(mixed $value): string
    {
        if (!is_scalar($value) || '' === trim((string) $value)) {
            return CategoryWorkflowState::DRAFT;
        }

        return CategoryWorkflowState::fromString(trim((string) $value))->value();
    }

    private function requiredPath(mixed $value): string
    {
        $normalized = $this->requiredString($value, 'path');
        if ('' === $normalized) {
            throw new \RuntimeException('Category path cannot be empty.');
        }

        return $normalized;
    }

    private function requiredString(mixed $value, string $field): string
    {
        if (!is_scalar($value)) {
            throw new \InvalidArgumentException($field.' is required');
        }

        $normalized = trim((string) $value);
        if ('' === $normalized) {
            throw new \InvalidArgumentException($field.' is required');
        }

        return $normalized;
    }

    private function nullableScalarToString(mixed $value): ?string
    {
        if (!is_scalar($value)) {
            return null;
        }

        $normalized = trim((string) $value);

        return '' === $normalized ? null : $normalized;
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

    private function levelFromPath(string $path): int
    {
        if ('' === $path) {
            return 0;
        }

        return substr_count($path, '.');
    }
}
