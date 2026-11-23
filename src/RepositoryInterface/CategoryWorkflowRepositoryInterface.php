<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\RepositoryInterface;

use App\Cataloging\EntityInterface\CategoryWorkflowInterface;
use App\Cataloging\EventInterface\CategoryWorkflowTransitionedInterface;

interface CategoryWorkflowRepositoryInterface
{
    public function findByCategoryId(string $categoryId): ?CategoryWorkflowInterface;

    public function save(CategoryWorkflowInterface $workflow): void;

    public function appendHistory(CategoryWorkflowTransitionedInterface $event): void;

    /** @return list<CategoryWorkflowTransitionedInterface> */
    public function historyForCategoryId(string $categoryId): array;
}
