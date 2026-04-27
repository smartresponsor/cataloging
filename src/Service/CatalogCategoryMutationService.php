<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\CatalogCategoryAuditEntity;
use App\Cataloging\Entity\CatalogCategoryEntity;
use App\Cataloging\Exception\CategoryNotFoundException;
use App\Cataloging\IdempotencyInterface\CategoryIdempotencyStoreInterface;
use App\Cataloging\PolicyInterface\CatalogCategoryWorkflowEntityPolicyInterface;
use App\Cataloging\ServiceInterface\CatalogCategoryMutationServiceInterface;
use App\Cataloging\ServiceInterface\CatalogPublicationGateServiceInterface;
use App\Cataloging\ValueObject\CatalogCategoryWorkflowEntityState;
use App\Cataloging\ValueObject\CategoryMutationMoveRequest;
use App\Cataloging\ValueObject\CategoryMutationPublishRequest;
use App\Cataloging\ValueObject\CategoryPublicationGateEvaluationRequest;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Provides the category mutation service application service.
 */
final class CatalogCategoryMutationService implements CatalogCategoryMutationServiceInterface
{
    private const int IDEMPOTENCY_TTL_SEC = 86400;

    /**
     * Initializes the category mutation service service collaborators.
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CatalogOutboxWriterService $outboxWriter,
        private readonly CacheInvalidationRecorder $cacheInvalidationRecorder,
        private readonly CatalogPublicationGateServiceInterface $publicationGateService,
        private readonly CatalogCategoryWorkflowEntityPolicyInterface $workflowPolicy,
        private readonly CategoryIdempotencyStoreInterface $idempotencyStore,
    ) {
    }

    /**
     * Handles the move workflow.
     *
     * @param CategoryMutationMoveRequest $request
     *
     * @return array{
     *     id:string,
     *     oldParentId:string|null,
     *     newParentId:string,
     *     treeId:string,
     *     policy:string,
     *     changedCount:int,
     *     dryRun:bool,
     *     redirects:list<array{id:string,from:string,to:string}>,
     *     duplicate:bool,
     * }
     *
     * @throws \JsonException
     * @throws \Throwable
     */
    public function move(CategoryMutationMoveRequest $request): array
    {
        $normalizedCategoryId = $this->requiredString($request->categoryId(), 'categoryId');
        $normalizedNewParentId = $this->requiredString($request->newParentId(), 'newParentId');
        $normalizedActorId = $this->requiredString($request->actorId(), 'actorId');
        $normalizedTreeId = $this->requiredString($request->treeId(), 'treeId');
        $normalizedPolicy = $this->requiredString($request->policy(), 'policy');
        $dryRun = $request->dryRun();
        $normalizedLocale = null !== $request->locale() ? trim($request->locale()) : null;
        $normalizedCorrelationId = $this->normalizeOptionalString($request->correlationId());
        $normalizedRequest = new CategoryMutationMoveRequest(
            $normalizedCategoryId,
            $normalizedNewParentId,
            $normalizedActorId,
            $normalizedTreeId,
            $normalizedPolicy,
            $dryRun,
            $normalizedLocale,
            $request->idempotencyKey(),
            $normalizedCorrelationId,
        );
        $commandKey = $this->moveIdempotencyKey($normalizedRequest);
        $requestHash = $this->moveRequestHash($normalizedRequest);

        if ($normalizedCategoryId === $normalizedNewParentId) {
            throw new \InvalidArgumentException('A node cannot be moved under itself.');
        }
        $this->entityManager->beginTransaction();

        try {
            if (
                !$dryRun
                && !$this->idempotencyStore->acquire(
                    $commandKey,
                    'category.move',
                    $requestHash,
                    self::IDEMPOTENCY_TTL_SEC,
                    $normalizedCorrelationId,
                )
            ) {
                $result = $this->duplicateMoveResult(
                    $normalizedCategoryId,
                    $normalizedNewParentId,
                    $normalizedTreeId,
                    $normalizedPolicy,
                );
                $this->entityManager->rollback();

                return $result;
            }

            $node = $this->fetchCategory($normalizedCategoryId);
            $newParent = $this->fetchCategory($normalizedNewParentId);

            $oldPath = $this->requiredPath($node['path'] ?? null);
            $newParentPath = $this->requiredPath($newParent['path'] ?? null);

            if ($newParentPath === $oldPath || str_starts_with($newParentPath, $oldPath.'.')) {
                throw new \InvalidArgumentException('Cannot move a node under its own descendant.');
            }

            $oldParentId = $this->nullableScalarToString($node['parent_id'] ?? null);
            if ($oldParentId === $normalizedNewParentId) {
                $this->entityManager->rollback();

                return [
                    'id' => $normalizedCategoryId,
                    'oldParentId' => $oldParentId,
                    'newParentId' => $normalizedNewParentId,
                    'treeId' => $normalizedTreeId,
                    'policy' => $normalizedPolicy,
                    'changedCount' => 0,
                    'dryRun' => $dryRun,
                    'redirects' => [],
                    'duplicate' => false,
                ];
            }

            $leafSegment = $this->lastSegment($oldPath);
            $newPath = '' !== $newParentPath ? $newParentPath.'.'.$leafSegment : $leafSegment;
            $subtree = $this->fetchSubtree($oldPath);

            $changedCount = 0;
            $redirects = [];
            foreach ($subtree as $row) {
                $currentPath = $row['path'];
                $rebasedPath = $this->rebasePath($currentPath, $oldPath, $newPath);
                if ($rebasedPath === $currentPath) {
                    continue;
                }

                $rowId = $row['id'];
                $depth = $this->levelFromPath($rebasedPath);
                $parentId = $rowId === $normalizedCategoryId
                    ? $normalizedNewParentId
                    : $row['parent_id'];

                $entity = $this->findCategoryEntity($rowId);
                if (!$entity instanceof CatalogCategoryEntity) {
                    throw new \RuntimeException(sprintf('Category node "%s" was not found for ORM update.', $rowId));
                }

                $entity->setPath($rebasedPath);
                $entity->setDepth($depth);
                $entity->setParentId($parentId);

                ++$changedCount;
                $redirects[] = ['id' => $rowId, 'from' => $currentPath, 'to' => $rebasedPath];
            }

            if ($dryRun) {
                $this->entityManager->rollback();

                return [
                    'id' => $normalizedCategoryId,
                    'oldParentId' => $oldParentId,
                    'newParentId' => $normalizedNewParentId,
                    'treeId' => $normalizedTreeId,
                    'policy' => $normalizedPolicy,
                    'changedCount' => $changedCount,
                    'dryRun' => true,
                    'redirects' => $redirects,
                    'duplicate' => false,
                ];
            }

            $this->writeAudit('category.move', [
                'categoryId' => $normalizedCategoryId,
                'actorId' => $normalizedActorId,
                'oldParentId' => $oldParentId,
                'newParentId' => $normalizedNewParentId,
                'treeId' => $normalizedTreeId,
                'policy' => $normalizedPolicy,
                'changedCount' => $changedCount,
                'redirects' => $redirects,
                'correlationId' => $normalizedCorrelationId,
                'idempotencyKey' => $commandKey,
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
                'correlationId' => $normalizedCorrelationId,
                'idempotencyKey' => $commandKey,
            ],
                sprintf(
                    'category.move:%s:%s:%s',
                    $normalizedCategoryId,
                    $normalizedNewParentId,
                    sha1(json_encode($redirects, JSON_THROW_ON_ERROR)),
                ),
            );

            $this->entityManager->flush();
            $this->entityManager->commit();

            $result = [
                'id' => $normalizedCategoryId,
                'oldParentId' => $oldParentId,
                'newParentId' => $normalizedNewParentId,
                'treeId' => $normalizedTreeId,
                'policy' => $normalizedPolicy,
                'changedCount' => $changedCount,
                'dryRun' => false,
                'redirects' => $redirects,
                'duplicate' => false,
            ];

            $this->cacheInvalidationRecorder->invalidate($result['id']);
        } catch (\Throwable $exception) {
            $this->rollbackIfActive();

            error_log('[CatalogCategoryMutationService] '.$exception->getMessage());

            if ($exception instanceof \InvalidArgumentException || $exception instanceof \RuntimeException || $exception instanceof \DomainException) {
                throw $exception;
            }

            throw new \RuntimeException('Move failed: '.$exception->getMessage(), 0, $exception);
        }

        return $result;
    }

    /**
     * Handles the publish workflow.
     *
     * @param CategoryMutationPublishRequest $request
     *
     * @return array{
     *     id:string,
     *     published:bool,
     *     workflowState:string,
     *     previousWorkflowState:string,
     *     blockers:list<string>,
     *     warnings:list<string>,
     *     checks:array<string,bool>,
     *     publishedAt:string|null,
     *     reason:string,
     *     duplicate:bool,
     * }
     *
     * @throws \JsonException
     * @throws \Throwable
     */
    public function publish(CategoryMutationPublishRequest $request): array
    {
        $normalizedCategoryId = $this->requiredString($request->categoryId(), 'categoryId');
        $published = $request->published();
        $normalizedActorId = $this->requiredString($request->actorId(), 'actorId');
        $normalizedReason = $this->requiredString($request->reason(), 'reason');
        $normalizedChecks = $this->normalizeChecks($request->checks());
        $normalizedCorrelationId = $this->normalizeOptionalString($request->correlationId());
        $normalizedRequest = new CategoryMutationPublishRequest(
            $normalizedCategoryId,
            $published,
            $normalizedChecks,
            $normalizedActorId,
            $normalizedReason,
            $request->idempotencyKey(),
            $normalizedCorrelationId,
        );
        $commandKey = $this->publishIdempotencyKey($normalizedRequest);
        $requestHash = $this->publishRequestHash($normalizedRequest);
        $this->entityManager->beginTransaction();

        try {
            if (
                !$this->idempotencyStore->acquire(
                    $commandKey,
                    $published ? 'category.publish' : 'category.unpublish',
                    $requestHash,
                    self::IDEMPOTENCY_TTL_SEC,
                    $normalizedCorrelationId,
                )
            ) {
                $result = $this->duplicatePublishResult(
                    $normalizedCategoryId,
                    $normalizedChecks,
                    $normalizedReason,
                );
                $this->entityManager->rollback();

                return $result;
            }

            $category = $this->fetchCategory($normalizedCategoryId);
            $currentWorkflowState = $this->workflowStateValue($category['workflow_state'] ?? null);
            $previousPublished = (bool) ($category['published'] ?? false);

            if ($published) {
                $gate = $this->publicationGateService->evaluate(new CategoryPublicationGateEvaluationRequest(
                    $normalizedCategoryId,
                    $currentWorkflowState,
                    $normalizedChecks,
                    $normalizedActorId,
                    $normalizedReason,
                ));
                $payload = $gate->payload();
                if (($payload['publishable'] ?? false) !== true) {
                    throw new \DomainException('Category publication gate failed: '.implode(',', $this->stringList(is_array($payload['blockers'] ?? null) ? $payload['blockers'] : [])));
                }
                $targetState = CatalogCategoryWorkflowEntityState::PUBLISHED;
                $publishedAtDateTime = new \DateTimeImmutable('now');
                $publishedAt = $publishedAtDateTime->format('Y-m-d H:i:s');
                $blockers = $this->stringList(is_array($payload['blockers'] ?? null) ? $payload['blockers'] : []);
                $warnings = $this->stringList(is_array($payload['warnings'] ?? null) ? $payload['warnings'] : []);
                $checksForResponse = $this->boolMap(is_array($payload['checks'] ?? null) ? $payload['checks'] : []);
            } else {
                $targetState = CatalogCategoryWorkflowEntityState::DRAFT;
                $publishedAt = null;
                $publishedAtDateTime = null;
                $blockers = [];
                $warnings = [];
                $checksForResponse = [];
            }

            $from = CatalogCategoryWorkflowEntityState::fromString($currentWorkflowState);
            $to = CatalogCategoryWorkflowEntityState::fromString($targetState);
            $this->workflowPolicy->assertTransitionAllowed($from, $to, $normalizedActorId, $normalizedReason);

            $entity = $this->findCategoryEntity($normalizedCategoryId);
            if (!$entity instanceof CatalogCategoryEntity) {
                throw new CategoryNotFoundException(sprintf('Category "%s" was not found.', $normalizedCategoryId));
            }

            $entity->setWorkflowState($targetState);
            $entity->setPublished($published);
            $entity->setPublishedAt(null === $publishedAtDateTime ? null : $publishedAtDateTime);

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
                'correlationId' => $normalizedCorrelationId,
                'idempotencyKey' => $commandKey,
            ];

            $this->writeAudit('category.publish', $payload);
            $this->outboxWriter->append(
                $published ? 'category.published' : 'category.unpublished',
                $payload,
                sprintf(
                    'category.publish:%s:%s:%s',
                    $normalizedCategoryId,
                    $published ? '1' : '0',
                    sha1($normalizedReason),
                ),
            );

            $this->entityManager->flush();
            $this->entityManager->commit();

            $result = [
                'id' => $normalizedCategoryId,
                'published' => $published,
                'workflowState' => $targetState,
                'previousWorkflowState' => $currentWorkflowState,
                'blockers' => $blockers,
                'warnings' => $warnings,
                'checks' => $checksForResponse,
                'publishedAt' => $publishedAt,
                'reason' => $normalizedReason,
                'duplicate' => false,
            ];

            $this->cacheInvalidationRecorder->invalidate($result['id']);
        } catch (\Throwable $exception) {
            $this->rollbackIfActive();

            error_log('[CatalogCategoryMutationService] '.$exception->getMessage());

            if ($exception instanceof \InvalidArgumentException || $exception instanceof \RuntimeException || $exception instanceof \DomainException) {
                throw $exception;
            }

            throw new \RuntimeException('Publish failed: '.$exception->getMessage(), 0, $exception);
        }

        return $result;
    }

    /**
     * @param string $categoryId
     * @param string $newParentId
     * @param string $treeId
     * @param string $policy
     *
     * @return array{
     *     id:string,
     *     oldParentId: ?string,
     *     newParentId:string,
     *     treeId:string,
     *     policy:string,
     *     changedCount:int,
     *     dryRun:bool,
     *     redirects:list<array{id:string,from:string,to:string}>,
     *     duplicate:bool,
     * }
     */
    private function duplicateMoveResult(
        string $categoryId,
        string $newParentId,
        string $treeId,
        string $policy,
    ): array {
        $category = $this->fetchCategory($categoryId);

        return [
            'id' => $categoryId,
            'oldParentId' => $this->nullableScalarToString($category['parent_id'] ?? null),
            'newParentId' => $newParentId,
            'treeId' => $treeId,
            'policy' => $policy,
            'changedCount' => 0,
            'dryRun' => false,
            'redirects' => [],
            'duplicate' => true,
        ];
    }

    /**
     * @param string             $categoryId
     * @param array<string,bool> $checks
     * @param string             $reason
     *
     * @return array{
     *     id:string,
     *     published:bool,
     *     workflowState:string,
     *     previousWorkflowState:string,
     *     blockers:list<string>,
     *     warnings:list<string>,
     *     checks:array<string,bool>,
     *     publishedAt: ?string,
     *     reason:string,
     *     duplicate:bool,
     * }
     */
    private function duplicatePublishResult(
        string $categoryId,
        array $checks,
        string $reason,
    ): array {
        $category = $this->fetchCategory($categoryId);
        $workflowState = $this->workflowStateValue($category['workflow_state'] ?? null);

        return [
            'id' => $categoryId,
            'published' => (bool) ($category['published'] ?? false),
            'workflowState' => $workflowState,
            'previousWorkflowState' => $workflowState,
            'blockers' => [],
            'warnings' => [],
            'checks' => $checks,
            'publishedAt' => $this->nullableScalarToString($category['published_at'] ?? null),
            'reason' => $reason,
            'duplicate' => true,
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function fetchCategory(string $categoryId): array
    {
        $entity = $this->findCategoryEntity($categoryId);
        if ($entity instanceof CatalogCategoryEntity) {
            return $this->mapCategoryEntity($entity);
        }

        throw new CategoryNotFoundException(sprintf('Category "%s" was not found.', $categoryId));
    }

    /**
     * @return list<array{id:string,parent_id:?string,path:string,depth:int}>
     */
    private function fetchSubtree(string $path): array
    {
        $rows = $this->entityManager->createQuery(
            'SELECT c.id AS id, c.parentId AS parent_id, c.path AS path, c.depth AS depth
             FROM App\Cataloging\Entity\CatalogCategoryEntity c
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
     * @return array{id:string,parent_id:?string,path:string,depth:int}
     */
    private function normalizeSubtreeRow(array $row): array
    {
        $path = $this->requiredPath($row['path']);

        return [
            'id' => $this->requiredString($row['id'], 'category row id'),
            'parent_id' => $this->nullableScalarToString($row['parent_id']),
            'path' => $path,
            'depth' => $this->levelFromPath($path),
        ];
    }

    private function findCategoryEntity(string $categoryId): ?CatalogCategoryEntity
    {
        $entity = $this->entityManager->getRepository(CatalogCategoryEntity::class)->find($categoryId);

        return $entity instanceof CatalogCategoryEntity ? $entity : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function mapCategoryEntity(CatalogCategoryEntity $entity): array
    {
        return [
            'id' => $entity->getId(),
            'parent_id' => $entity->getParentId(),
            'path' => $entity->getPath(),
            'depth' => $entity->getDepth(),
            'workflow_state' => $entity->getWorkflowState(),
            'published' => $entity->isPublished(),
            'published_at' => $entity->getPublishedAt()?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @param array<string,mixed> $payload
     */
    private function writeAudit(string $action, array $payload): void
    {
        $this->entityManager->persist(new CatalogCategoryAuditEntity($action, $payload));
    }

    private function rollbackIfActive(): void
    {
        try {
            $this->entityManager->rollback();
        } catch (\Throwable) {
        }
    }

    /**
     * @param array<string,bool> $checks
     *
     * @return array<string,bool>
     */
    private function normalizeChecks(array $checks): array
    {
        $normalized = array_map(function ($value) {
            return (bool) $value;
        }, $checks);

        ksort($normalized);

        return $normalized;
    }

    /**
     * @noinspection PhpPluralMixedCanBeReplacedWithArrayInspection
     *
     * @param array $values
     *
     * @phpstan-param array<mixed> $values
     *
     * @return list<string>
     */
    private function stringList(array $values): array
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

        return $normalized;
    }

    /**
     * @noinspection PhpPluralMixedCanBeReplacedWithArrayInspection
     *
     * @param array $values
     *
     * @phpstan-param array<mixed> $values
     *
     * @return array<string,bool>
     */
    private function boolMap(array $values): array
    {
        $normalized = [];
        foreach ($values as $key => $value) {
            if (!is_string($key)) {
                continue;
            }
            $normalized[$key] = (bool) $value;
        }

        ksort($normalized);

        return $normalized;
    }

    /**
     * @throws \ValueError
     */
    private function workflowStateValue(mixed $value): string
    {
        if (!is_scalar($value) || '' === trim((string) $value)) {
            return CatalogCategoryWorkflowEntityState::DRAFT;
        }

        return CatalogCategoryWorkflowEntityState::fromString(trim((string) $value))->value();
    }

    /**
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    private function requiredPath(mixed $value): string
    {
        $normalized = $this->requiredString($value, 'path');
        if ('' === $normalized) {
            throw new \RuntimeException('Category path cannot be empty.');
        }

        return $normalized;
    }

    /**
     * @throws \InvalidArgumentException
     */
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

    private function normalizeOptionalString(?string $value): ?string
    {
        if (null === $value) {
            return null;
        }

        $normalized = trim($value);

        return '' === $normalized ? null : $normalized;
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

    /**
     * @throws \JsonException
     */
    private function moveIdempotencyKey(CategoryMutationMoveRequest $request): string
    {
        $providedKey = $this->normalizeOptionalString($request->idempotencyKey());
        if (null !== $providedKey) {
            return sprintf('category.move:client:%s', $providedKey);
        }

        return sprintf('category.move:auto:%s', $this->moveRequestHash($request));
    }

    /**
     * @throws \JsonException
     */
    private function publishIdempotencyKey(CategoryMutationPublishRequest $request): string
    {
        $providedKey = $this->normalizeOptionalString($request->idempotencyKey());
        if (null !== $providedKey) {
            return sprintf('category.publish:client:%s', $providedKey);
        }

        return sprintf('category.publish:auto:%s', $this->publishRequestHash($request));
    }

    /**
     * @throws \JsonException
     */
    private function moveRequestHash(CategoryMutationMoveRequest $request): string
    {
        return sha1(json_encode([
            'categoryId' => $request->categoryId(),
            'newParentId' => $request->newParentId(),
            'actorId' => $request->actorId(),
            'treeId' => $request->treeId(),
            'policy' => $request->policy(),
            'dryRun' => $request->dryRun(),
            'locale' => $request->locale(),
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
    }

    /**
     * @throws \JsonException
     */
    private function publishRequestHash(CategoryMutationPublishRequest $request): string
    {
        return sha1(json_encode([
            'categoryId' => $request->categoryId(),
            'published' => $request->published(),
            'checks' => $request->checks(),
            'actorId' => $request->actorId(),
            'reason' => $request->reason(),
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
    }
}
