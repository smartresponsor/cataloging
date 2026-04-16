<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents the category pin domain record.
 */
#[ORM\Entity]
#[ORM\Table(name: 'catalog_category_pin')]
#[ORM\UniqueConstraint(name: 'uniq_category_pin', columns: ['category_id', 'record_id'])]
class CatalogCategoryPin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id = 0;

    #[ORM\Column(type: 'string', length: 26)]
    private string $categoryId;

    #[ORM\Column(type: 'string', length: 64)]
    private string $recordId;

    #[ORM\Column(type: 'integer')]
    private int $position;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /**
     * Initializes the category pin service collaborators.
     */
    public function __construct(string $categoryId, string $recordId, int $position = 0)
    {
        $this->categoryId = $categoryId;
        $this->recordId = $recordId;
        $this->position = $position;
        $this->createdAt = new \DateTimeImmutable('now');
    }

    /**
     * Handles the id workflow.
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string
    {
        return $this->categoryId;
    }

    /**
     * Handles the record id workflow.
     */
    public function recordId(): string
    {
        return $this->recordId;
    }

    /**
     * Handles the position workflow.
     */
    public function position(): int
    {
        return $this->position;
    }

    /**
     * Updates the position value.
     */
    public function setPosition(int $p): void
    {
        $this->position = $p;
    }

    /**
     * Creates the d at result for the current workflow.
     */
    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
