<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents the category banner domain record.
 */
#[ORM\Entity]
#[ORM\Table(name: 'catalog_category_banner')]
class CatalogCategoryBanner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id = 0;

    #[ORM\Column(type: 'string', length: 26)]
    private string $categoryId;

    #[ORM\Column(type: 'string', length: 160)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'boolean')]
    private bool $isDraft = true;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    /**
     * Initializes the category banner service collaborators.
     */
    public function __construct(string $categoryId, string $title, string $content)
    {
        $this->categoryId = $categoryId;
        $this->title = $title;
        $this->content = $content;
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
     * Handles the title workflow.
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * Handles the content workflow.
     */
    public function content(): string
    {
        return $this->content;
    }

    /**
     * Determines whether the draft condition is satisfied.
     */
    public function isDraft(): bool
    {
        return $this->isDraft;
    }

    /**
     * Handles the published at workflow.
     */
    public function publishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    /**
     * Handles the publish workflow.
     */
    public function publish(): void
    {
        $this->isDraft = false;
        $this->publishedAt = new \DateTimeImmutable('now');
    }
}
