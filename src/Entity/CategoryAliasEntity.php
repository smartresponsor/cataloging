<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Represents the category alias entity domain record.
 */
#[ORM\Entity]
#[ORM\Table(name: 'category_alias')]
class CategoryAliasEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id = 0;

    #[ORM\Column(type: 'string', length: 180)]
    private string $oldSlug;

    #[ORM\Column(type: 'string', length: 26)]
    private string $categoryId;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;
    /**
     * Initializes the category alias entity service collaborators.
     */
    public function __construct(string $oldSlug, string $categoryId)
    {
        $this->oldSlug = $oldSlug;
        $this->categoryId = $categoryId;
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
     * Handles the old slug workflow.
     */
    public function oldSlug(): string
    {
        return $this->oldSlug;
    }
    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string
    {
        return $this->categoryId;
    }
    /**
     * Creates the d at result for the current workflow.
     */
    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
