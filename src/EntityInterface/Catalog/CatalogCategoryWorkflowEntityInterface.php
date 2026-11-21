<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\EntityInterface\Catalog;

use App\Cataloging\ValueObjectInterface\CatalogCategoryWorkflowEntityStateInterface;

/**
 * Defines the contract for category workflow.
 */
interface CatalogCategoryWorkflowEntityInterface
{
    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string;

    /**
     * Handles the state workflow.
     */
    public function state(): CatalogCategoryWorkflowEntityStateInterface;

    /**
     * Handles the actor id workflow.
     */
    public function actorId(): string;

    /**
     * Handles the reason workflow.
     */
    public function reason(): string;

    /**
     * Handles the transitioned at workflow.
     */
    public function transitionedAt(): \DateTimeImmutable;
}
