<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\RepositoryInterface;

use App\Cataloging\EntityInterface\CategoryWorkflowInterface;
use App\Cataloging\EventInterface\CategoryWorkflowTransitionedInterface;

/**
 * Defines the contract for category workflow repository.
 */
interface CategoryWorkflowRepositoryInterface
{
    /**
     * Handles the find by category id workflow.
     */
    public function findByCategoryId(string $categoryId): ?CategoryWorkflowInterface;

    /**
     * Handles the save workflow.
     */
    public function save(CategoryWorkflowInterface $workflow): void;

    /**
     * Handles the append history workflow.
     */
    public function appendHistory(CategoryWorkflowTransitionedInterface $event): void;

    /** @return list<CategoryWorkflowTransitionedInterface> */
    public function historyForCategoryId(string $categoryId): array;
}
