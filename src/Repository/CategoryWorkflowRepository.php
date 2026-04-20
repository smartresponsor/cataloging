<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Repository;

use App\Cataloging\EntityInterface\CategoryWorkflowInterface;
use App\Cataloging\EventInterface\CategoryWorkflowTransitionedInterface;
use App\Cataloging\RepositoryInterface\CategoryWorkflowRepositoryInterface;

/**
 * Provides repository services for category workflow repository.
 */
final class CategoryWorkflowRepository implements CategoryWorkflowRepositoryInterface
{
    /** @var array<string,CategoryWorkflowInterface> */
    private array $current = [];

    /** @var array<string,list<CategoryWorkflowTransitionedInterface>> */
    private array $history = [];

    /**
     * Handles the find by category id workflow.
     */
    public function findByCategoryId(string $categoryId): ?CategoryWorkflowInterface
    {
        return $this->current[$categoryId] ?? null;
    }

    /**
     * Handles the save workflow.
     */
    public function save(CategoryWorkflowInterface $workflow): void
    {
        $this->current[$workflow->categoryId()] = $workflow;
    }

    /**
     * Handles the append history workflow.
     */
    public function appendHistory(CategoryWorkflowTransitionedInterface $event): void
    {
        $payload = $event->payload();
        $categoryId = isset($payload['categoryId']) && is_scalar($payload['categoryId'])
            ? trim((string) $payload['categoryId'])
            : '';

        $this->history[$categoryId] ??= [];
        $this->history[$categoryId][] = $event;
    }

    /**
     * Handles the history for category id workflow.
     */
    public function historyForCategoryId(string $categoryId): array
    {
        return $this->history[$categoryId] ?? [];
    }
}
