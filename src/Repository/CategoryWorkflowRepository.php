<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Repository;

use App\EntityInterface\CategoryWorkflowInterface;
use App\EventInterface\CategoryWorkflowTransitionedInterface;
use App\RepositoryInterface\CategoryWorkflowRepositoryInterface;

final class CategoryWorkflowRepository implements CategoryWorkflowRepositoryInterface
{
    /** @var array<string,CategoryWorkflowInterface> */
    private array $current = [];

    /** @var array<string,list<CategoryWorkflowTransitionedInterface>> */
    private array $history = [];

    public function findByCategoryId(string $categoryId): ?CategoryWorkflowInterface
    {
        return $this->current[$categoryId] ?? null;
    }

    public function save(CategoryWorkflowInterface $workflow): void
    {
        $this->current[$workflow->categoryId()] = $workflow;
    }

    public function appendHistory(CategoryWorkflowTransitionedInterface $event): void
    {
        $payload = $event->payload();
        $categoryId = (string) ($payload['categoryId'] ?? '');
        $this->history[$categoryId] ??= [];
        $this->history[$categoryId][] = $event;
    }

    public function historyForCategoryId(string $categoryId): array
    {
        return $this->history[$categoryId] ?? [];
    }
}
