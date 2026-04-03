<?php

declare(strict_types=1);

namespace App\ServiceInterface;

interface CategoryMutationServiceInterface
{
    /**
     * @return array{
     *   id:string,
     *   oldParentId:?string,
     *   newParentId:string,
     *   treeId:string,
     *   policy:string,
     *   changedCount:int,
     *   dryRun:bool,
     *   redirects:list<array{id:string,from:string,to:string}>,
     *   duplicate:bool
     * }
     */
    public function move(string $categoryId, string $newParentId, string $actorId, string $treeId = 'catalog', string $policy = 'strict', bool $dryRun = false, ?string $locale = null, ?string $idempotencyKey = null, ?string $correlationId = null): array;

    /**
     * @param array<string,bool> $checks
     *
     * @return array{
     *   id:string,
     *   published:bool,
     *   workflowState:string,
     *   previousWorkflowState:string,
     *   blockers:list<string>,
     *   warnings:list<string>,
     *   checks:array<string,bool>,
     *   publishedAt:?string,
     *   reason:string,
     *   duplicate:bool
     * }
     */
    public function publish(string $categoryId, bool $published, array $checks, string $actorId, string $reason, ?string $idempotencyKey = null, ?string $correlationId = null): array;
}
