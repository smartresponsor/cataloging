<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Entity\Catalog;

/** Universal link of Category to any target domain entity. */
final class CatalogCategoryLinkEntity
{
    /** @var string ULID */
    private string $id;
    private string $taxonomyId; // ULID
    private string $categoryId; // ULID
    private string $targetDomain;
    private string $targetClass;
    private string $targetId; // ULID
    private \DateTimeImmutable $createdAt;

    /**
     * Initializes the category link service collaborators.
     */
    public function __construct(
        string $id,
        string $taxonomyId,
        string $categoryId,
        string $targetDomain,
        string $targetClass,
        string $targetId,
        \DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->taxonomyId = $taxonomyId;
        $this->categoryId = $categoryId;
        $this->targetDomain = $targetDomain;
        $this->targetClass = $targetClass;
        $this->targetId = $targetId;
        $this->createdAt = $createdAt;
    }

    /**
     * Handles the id workflow.
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Handles the taxonomy id workflow.
     */
    public function taxonomyId(): string
    {
        return $this->taxonomyId;
    }

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string
    {
        return $this->categoryId;
    }

    /**
     * Handles the target domain workflow.
     */
    public function targetDomain(): string
    {
        return $this->targetDomain;
    }

    /**
     * Handles the target class workflow.
     */
    public function targetClass(): string
    {
        return $this->targetClass;
    }

    /**
     * Handles the target id workflow.
     */
    public function targetId(): string
    {
        return $this->targetId;
    }

    /**
     * Creates the d at result for the current workflow.
     */
    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
