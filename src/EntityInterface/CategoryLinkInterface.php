<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\EntityInterface;

/**
 * Defines the contract for category link.
 */
interface CatalogCategoryLinkEntityInterface
{
    /**
     * Handles the id workflow.
     */
    public function id(): string;

    /**
     * Handles the taxonomy id workflow.
     */
    public function taxonomyId(): string;

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string;

    /**
     * Handles the target domain workflow.
     */
    public function targetDomain(): string;

    /**
     * Handles the target class workflow.
     */
    public function targetClass(): string;

    /**
     * Handles the target id workflow.
     */
    public function targetId(): string;

    /**
     * Creates the d at result for the current workflow.
     */
    public function createdAt(): \DateTimeImmutable;
}
if (!class_exists(__NAMESPACE__.'\\CategoryLinkInterface', false)) {
    class_alias(CatalogCategoryLinkEntityInterface::class, __NAMESPACE__.'\\CategoryLinkInterface');
}
