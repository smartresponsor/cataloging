<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a historical slug recorded for a catalog category.
 */
#[ORM\Entity]
#[ORM\Table(name: 'category_slug_history')]
class CatalogCategorySlugHistoryEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id = 0;

    #[ORM\Column(type: 'string', length: 180)]
    private string $slug;

    #[ORM\Column(type: 'string', length: 26)]
    private string $categoryId;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /**
     * Initializes the category slug history record.
     */
    public function __construct(string $slug, string $categoryId)
    {
        $this->slug = $slug;
        $this->categoryId = $categoryId;
        $this->createdAt = new \DateTimeImmutable('now');
    }

    /**
     * Returns the surrogate history identifier.
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * Returns the historical slug stored for the category.
     */
    public function slug(): string
    {
        return $this->slug;
    }

    /**
     * Returns the durable category identifier.
     */
    public function categoryId(): string
    {
        return $this->categoryId;
    }

    /**
     * Returns the history record creation timestamp.
     */
    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
