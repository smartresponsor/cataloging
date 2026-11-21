<?php

declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\ValueObject\CategoryMutationMoveRequest;
use App\Cataloging\ValueObject\CategoryMutationPublishRequest;

/**
 * Defines the contract for category mutation service.
 */
interface CatalogCategoryMutationServiceInterface
{
    /**
     * @return array{
     *   id:string,
     *   'oldParentId':?string,
     *   'newParentId':string,
     *   'treeId':string,
     *   policy:string,
     *   changedCount:int,
     *   dryRun:bool,
     *   redirects:list<array{id:string,from:string,to:string}>,
     *   duplicate:bool
     * }
     */
    public function move(CategoryMutationMoveRequest $request): array;

    /**
     * @return array{
     *   id:string,
     *   published:bool,
     *   workflowState:string,
     *   previousWorkflowState:string,
     *   blockers:list<string>,
     *   warnings:list<string>,
     *   checks:array<string,bool>,
     *   'publishedAt':?string,
     *   reason:string,
     *   duplicate:bool
     * }
     */
    public function publish(CategoryMutationPublishRequest $request): array;
}
