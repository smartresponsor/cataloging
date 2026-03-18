<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\EntityInterface;

use App\ValueObjectInterface\CategoryWorkflowStateInterface;

interface CategoryWorkflowInterface
{
    public function categoryId(): string;

    public function state(): CategoryWorkflowStateInterface;

    public function actorId(): string;

    public function reason(): string;

    public function transitionedAt(): \DateTimeImmutable;
}
