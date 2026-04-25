<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\RepositoryInterface;

use App\Cataloging\EntityInterface\CatalogCategoryWorkflowEntityInterface;
use App\Cataloging\EventInterface\CatalogCategoryWorkflowEntityTransitionedInterface;

/**
 * Defines the contract for category workflow repository.
 */
interface CatalogCategoryWorkflowEntityRepositoryInterface
{
    /**
     * Handles the find by category id workflow.
     */
    public function findByCategoryId(string $categoryId): ?CatalogCategoryWorkflowEntityInterface;

    /**
     * Handles the save workflow.
     */
    public function save(CatalogCategoryWorkflowEntityInterface $workflow): void;

    /**
     * Handles the append history workflow.
     */
    public function appendHistory(CatalogCategoryWorkflowEntityTransitionedInterface $event): void;

    /** @return list<CatalogCategoryWorkflowEntityTransitionedInterface> */
    public function historyForCategoryId(string $categoryId): array;
}
if (!class_exists(__NAMESPACE__.'\\CategoryWorkflowRepositoryInterface', false)) {
    class_alias(CatalogCategoryWorkflowEntityRepositoryInterface::class, __NAMESPACE__.'\\CategoryWorkflowRepositoryInterface');
}
